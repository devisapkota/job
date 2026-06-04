from flask import Flask, request, jsonify
from flask_cors import CORS
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import pdfplumber
import re

app = Flask(__name__)
CORS(app)

SKILL_KEYWORDS = [
    "html", "css", "javascript", "react", "php", "mysql",
    "python", "flask", "django", "api", "sql", "excel",
    "data analysis", "machine learning", "ai", "java",
    "c++", "bootstrap", "laravel", "node.js"
]

def clean_text(text):
    text = text.lower()
    text = re.sub(r"[^a-zA-Z0-9+#. ]", " ", text)
    text = re.sub(r"\s+", " ", text)
    return text.strip()

def extract_skills(text):
    text = clean_text(text)
    found = []

    for skill in SKILL_KEYWORDS:
        if skill.lower() in text:
            found.append(skill)

    return list(set(found))

def calculate_missing_skills(user_skills, required_skills):
    user_set = set([s.strip().lower() for s in user_skills])
    required_set = set([s.strip().lower() for s in required_skills.split(",")])
    missing = required_set - user_set
    return list(missing)

@app.route("/recommend", methods=["POST"])
def recommend():
    data = request.json

    user_skills_text = data.get("skills", "")
    jobs = data.get("jobs", [])

    if not user_skills_text or not jobs:
        return jsonify([])

    user_text = clean_text(user_skills_text)

    documents = [user_text]

    for job in jobs:
        job_text = job["title"] + " " + job["description"] + " " + job["required_skills"]
        documents.append(clean_text(job_text))

    vectorizer = TfidfVectorizer()
    vectors = vectorizer.fit_transform(documents)

    user_vector = vectors[0]
    job_vectors = vectors[1:]

    similarity_scores = cosine_similarity(user_vector, job_vectors)[0]

    user_skills = extract_skills(user_skills_text)

    recommendations = []

    for index, score in enumerate(similarity_scores):
        job = jobs[index]
        match_score = round(float(score) * 100, 2)

        if match_score > 0:
            missing_skills = calculate_missing_skills(
                user_skills,
                job["required_skills"]
            )

            recommendations.append({
                "job_id": job["job_id"],
                "title": job["title"],
                "company": job["company"],
                "location": job["location"],
                "salary": job["salary"],
                "match_score": match_score,
                "missing_skills": missing_skills
            })

    recommendations = sorted(
        recommendations,
        key=lambda x: x["match_score"],
        reverse=True
    )

    return jsonify(recommendations[:5])

@app.route("/parse-resume", methods=["POST"])
def parse_resume():
    data = request.json
    file_path = data.get("file_path")

    text = ""

    try:
        with pdfplumber.open(file_path) as pdf:
            for page in pdf.pages:
                page_text = page.extract_text()
                if page_text:
                    text += page_text + " "

        skills = extract_skills(text)

        return jsonify({
            "text": text,
            "skills": skills
        })

    except Exception as e:
        return jsonify({
            "text": "",
            "skills": [],
            "error": str(e)
        })

if __name__ == "__main__":
    app.run(debug=True)