import re
import os
import PyPDF2

from flask import Flask, request, jsonify

app = Flask(__name__)

# =====================================
# Known Skills Database
# =====================================

KNOWN_SKILLS = [
    "python",
    "java",
    "php",
    "html",
    "css",
    "javascript",
    "mysql",
    "sql",
    "flask",
    "django",
    "laravel",
    "react",
    "node.js",
    "bootstrap",
    "ajax",
    "machine learning",
    "data analysis",
    "excel",
    "git",
    "github",
    "c++",
    "c#"
]

# =====================================
# Resume Keywords
# =====================================

RESUME_KEYWORDS = [
    "education",
    "experience",
    "skills",
    "projects",
    "certification",
    "objective",
    "summary",
    "contact",
    "email",
    "phone",
    "work experience"
]

# =====================================
# Home Route
# =====================================

@app.route("/")
def home():
    return "AI Job Recommendation Flask Server Running"

# =====================================
# Job Recommendation Route
# =====================================

@app.route("/recommend", methods=["POST"])
def recommend():

    data = request.get_json()

    skills = data.get("skills", "").lower()
    jobs = data.get("jobs", [])

    user_skills = [
        s.strip().lower()
        for s in skills.split(",")
        if s.strip()
    ]

    recommendations = []

    for job in jobs:

        required_skills = [
            s.strip().lower()
            for s in job["required_skills"].split(",")
            if s.strip()
        ]

        matched = []
        missing = []

        for skill in required_skills:
            if skill in user_skills:
                matched.append(skill)
            else:
                missing.append(skill)

        if len(required_skills) > 0:
            score = round((len(matched) / len(required_skills)) * 100, 2)
        else:
            score = 0

        job["match_score"] = score
        job["missing_skills"] = missing

        recommendations.append(job)

    recommendations = sorted(
        recommendations,
        key=lambda x: x["match_score"],
        reverse=True
    )

    return jsonify(recommendations)

# =====================================
# Resume Parsing + ATS Route
# =====================================

@app.route("/parse-resume", methods=["POST"])
def parse_resume():

    data = request.get_json()
    file_path = data.get("file_path")

    if not file_path:
        return jsonify({
            "success": False,
            "message": "No file path received.",
            "skills": [],
            "ats_score": 0,
            "suggestions": []
        })

    if not os.path.exists(file_path):
        return jsonify({
            "success": False,
            "message": "PDF file not found.",
            "skills": [],
            "ats_score": 0,
            "suggestions": []
        })

    if not file_path.lower().endswith(".pdf"):
        return jsonify({
            "success": False,
            "message": "Only PDF files are allowed.",
            "skills": [],
            "ats_score": 0,
            "suggestions": []
        })

    try:
        text = ""

        with open(file_path, "rb") as file:
            reader = PyPDF2.PdfReader(file)

            for page in reader.pages:
                page_text = page.extract_text()

                if page_text:
                    text += page_text + " "

        if len(text.strip()) < 50:
            return jsonify({
                "success": False,
                "message": "Unable to extract enough text.",
                "skills": [],
                "ats_score": 0,
                "suggestions": ["Use a text-based PDF instead of scanned image PDF."]
            })

        text_lower = text.lower()

        keyword_count = 0

        for keyword in RESUME_KEYWORDS:
            if keyword in text_lower:
                keyword_count += 1

        email_found = re.search(
            r"[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+",
            text
        )

        phone_found = re.search(
            r"(\+?\d[\d\s\-]{7,}\d)",
            text
        )

        if keyword_count < 2 and not email_found:
            return jsonify({
                "success": False,
                "message": "This PDF does not appear to be a resume.",
                "skills": [],
                "ats_score": 0,
                "suggestions": ["Upload a proper resume with education, skills, experience, email and phone."]
            })

        found_skills = []

        for skill in KNOWN_SKILLS:
            pattern = r"\b" + re.escape(skill) + r"\b"

            if re.search(pattern, text_lower):
                found_skills.append(skill.title())

        # =====================================
        # ATS Score Calculation
        # Total = 100
        # =====================================

        ats_score = 0
        suggestions = []

        if email_found:
            ats_score += 15
        else:
            suggestions.append("Add email address.")

        if phone_found:
            ats_score += 10
        else:
            suggestions.append("Add phone number.")

        if "education" in text_lower:
            ats_score += 15
        else:
            suggestions.append("Add education section.")

        if "experience" in text_lower or "work experience" in text_lower:
            ats_score += 20
        else:
            suggestions.append("Add work experience section.")

        if "skills" in text_lower:
            ats_score += 20
        else:
            suggestions.append("Add skills section.")

        if "projects" in text_lower:
            ats_score += 10
        else:
            suggestions.append("Add projects section.")

        if len(found_skills) >= 3:
            ats_score += 10
        else:
            suggestions.append("Add more technical skills.")

        if ats_score > 100:
            ats_score = 100

        return jsonify({
            "success": True,
            "message": "Resume processed successfully.",
            "skills": found_skills,
            "ats_score": ats_score,
            "suggestions": suggestions,
            "resume_score": keyword_count,
            "email_found": bool(email_found),
            "phone_found": bool(phone_found)
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "message": str(e),
            "skills": [],
            "ats_score": 0,
            "suggestions": []
        })

# =====================================
# Start Flask Server
# =====================================

if __name__ == "__main__":
    app.run(
        debug=True,
        host="127.0.0.1",
        port=5000
    )