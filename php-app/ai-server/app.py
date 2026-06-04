import re
import os
import math
import PyPDF2

from flask import Flask, request, jsonify

app = Flask(__name__)

KNOWN_SKILLS = [
    "python", "java", "php", "html", "css", "javascript",
    "mysql", "sql", "flask", "django", "laravel",
    "react", "node.js", "bootstrap", "ajax",
    "machine learning", "data analysis", "excel",
    "git", "github", "c++", "c#"
]

RESUME_KEYWORDS = [
    "education", "experience", "skills", "projects",
    "certification", "objective", "summary", "contact",
    "email", "phone", "work experience"
]


def tokenize(text):
    text = text.lower()
    return re.findall(r"[a-zA-Z0-9+#.]+", text)


def tfidf_cosine_similarity(user_text, job_texts):
    documents = [user_text] + job_texts
    tokenized_docs = [tokenize(doc) for doc in documents]

    vocabulary = sorted(set(word for doc in tokenized_docs for word in doc))

    if not vocabulary:
        return [0 for _ in job_texts]

    total_docs = len(tokenized_docs)

    idf = {}

    for word in vocabulary:
        docs_with_word = sum(1 for doc in tokenized_docs if word in doc)
        idf[word] = math.log((total_docs + 1) / (docs_with_word + 1)) + 1

    vectors = []

    for doc in tokenized_docs:
        vector = []

        for word in vocabulary:
            term_count = doc.count(word)
            total_terms = len(doc)

            tf = term_count / total_terms if total_terms > 0 else 0
            vector.append(tf * idf[word])

        vectors.append(vector)

    user_vector = vectors[0]
    job_vectors = vectors[1:]

    similarities = []

    for job_vector in job_vectors:
        dot_product = sum(a * b for a, b in zip(user_vector, job_vector))
        user_norm = math.sqrt(sum(a * a for a in user_vector))
        job_norm = math.sqrt(sum(b * b for b in job_vector))

        if user_norm == 0 or job_norm == 0:
            similarities.append(0)
        else:
            similarities.append(dot_product / (user_norm * job_norm))

    return similarities


@app.route("/")
def home():
    return "AI Job Recommendation Flask Server Running"


@app.route("/recommend", methods=["POST"])
def recommend():

    data = request.get_json()

    user_skills = data.get("skills", "")
    jobs = data.get("jobs", [])

    if not user_skills or len(jobs) == 0:
        return jsonify([])

    job_texts = []

    for job in jobs:
        job_text = (
            job.get("title", "") + " " +
            job.get("description", "") + " " +
            job.get("required_skills", "")
        )
        job_texts.append(job_text)

    similarity_scores = tfidf_cosine_similarity(user_skills, job_texts)

    recommendations = []

    for index, job in enumerate(jobs):

        score = round(similarity_scores[index] * 100, 2)

        user_skill_list = [
            s.strip().lower()
            for s in user_skills.split(",")
            if s.strip()
        ]

        required_skill_list = [
            s.strip().lower()
            for s in job.get("required_skills", "").split(",")
            if s.strip()
        ]

        missing_skills = [
            skill
            for skill in required_skill_list
            if skill not in user_skill_list
        ]

        job["match_score"] = score
        job["missing_skills"] = missing_skills

        recommendations.append(job)

    recommendations = sorted(
        recommendations,
        key=lambda x: x["match_score"],
        reverse=True
    )

    return jsonify(recommendations)


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

        if len(text.strip()) < 80:
            return jsonify({
                "success": False,
                "message": "This PDF is not accepted. Please upload a text-based resume PDF.",
                "skills": [],
                "ats_score": 0,
                "suggestions": [
                    "Use a proper text-based resume PDF, not a scanned image or unrelated PDF."
                ]
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

        if keyword_count < 3 or not email_found:
            return jsonify({
                "success": False,
                "message": "This PDF is not accepted. Please upload a valid resume containing Education, Skills, Experience and Email.",
                "skills": [],
                "ats_score": 0,
                "suggestions": [
                    "Upload a proper resume with Education, Skills, Experience and Email."
                ]
            })

        found_skills = []

        for skill in KNOWN_SKILLS:
            pattern = r"\b" + re.escape(skill) + r"\b"

            if re.search(pattern, text_lower):
                found_skills.append(skill.title())

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


if __name__ == "__main__":
    app.run(
        debug=True,
        host="127.0.0.1",
        port=5000
    )