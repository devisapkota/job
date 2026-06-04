import requests
from bs4 import BeautifulSoup
import mysql.connector
import time

db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="job_recommendation"
)

cursor = db.cursor()

url = "https://merojob.com/"

headers = {
    "User-Agent": "Mozilla/5.0"
}

bad_titles = [
    "search", "browse jobs", "trainings", "events", "register",
    "for employers", "all jobs", "jobs by function", "jobs by title",
    "jobs by industry", "jobs by location", "login", "career tips"
]

response = requests.get(url, headers=headers)
soup = BeautifulSoup(response.text, "html.parser")

links = soup.find_all("a", href=True)

saved_count = 0

for link in links:
    title = link.get_text(strip=True)
    href = link.get("href")

    if not title or len(title) < 4:
        continue

    title_lower = title.lower()

    if title_lower in bad_titles:
        continue

    if "http" not in href:
        href = "https://merojob.com" + href

    company = "Merojob"
    description = "Job imported from merojob.com. Link: " + href
    skills = "Not specified"
    location = "Nepal"
    salary = 0

    check_sql = "SELECT job_id FROM jobs WHERE title=%s AND company=%s LIMIT 1"
    cursor.execute(check_sql, (title, company))

    if cursor.fetchone():
        print("Skipped duplicate:", title)
        continue

    sql = """
    INSERT INTO jobs(title, company, description, required_skills, location, salary)
    VALUES(%s, %s, %s, %s, %s, %s)
    """

    values = (title, company, description, skills, location, salary)

    try:
        cursor.execute(sql, values)
        db.commit()
        saved_count += 1
        print("Saved:", title)
    except Exception as e:
        print("Error:", e)

    time.sleep(0.5)

cursor.close()
db.close()

print("Total saved:", saved_count)