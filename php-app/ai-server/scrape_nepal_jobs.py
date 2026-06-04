import requests
from bs4 import BeautifulSoup
import mysql.connector
import time
import re

# Database connection
db = mysql.connector.connect( host="localhost", user="root", password="", database="job_recommendation" )
cursor = db.cursor()

BAD_TITLES = [
    "search", "browse jobs", "trainings", "events", "register", "login", "career tips",
    "post a job", "find jobs", "how it works", "about us", "contact us", "privacy policy",
    "terms of service", "faq", "blog", "facebook", "twitter", "linkedin", "instagram", "youtube"
]

def save_job(title, company, href, source_name):
    if not title or len(title) < 5: return False # Relaxed from 8
    title = title.strip()
    title_lower = title.lower()
    for bad in BAD_TITLES:
        if bad in title_lower: return False
    
    check_sql = "SELECT job_id FROM jobs WHERE title=%s AND company=%s LIMIT 1"
    cursor.execute(check_sql, (title, company))
    if cursor.fetchone(): return False

    description = f"External job from {source_name}. View details at: {href}"
    sql = "INSERT INTO jobs(title, company, description, required_skills, location, salary, is_external) VALUES(%s, %s, %s, %s, %s, %s, %s)"
    cursor.execute(sql, (title, company, description, "Refer to link", "Nepal", 0, 1))
    db.commit()
    print(f"[{source_name}] Saved: {title}")
    return True

def scrape_site(url, name, link_pattern):
    print(f"Scraping {name}...")
    headers = {"User-Agent": "Mozilla/5.0"}
    try:
        response = requests.get(url, headers=headers, timeout=10)
        soup = BeautifulSoup(response.text, "html.parser")
        links = soup.find_all("a", href=True)
        saved = 0
        limit = 25 # Increased limit to 25
        for link in links:
            if saved >= limit: break
            href = link.get("href")
            title = link.get_text(strip=True)
            if re.search(link_pattern, href, re.IGNORECASE) and len(title) > 5: # Relaxed from 10
                if "http" not in href:
                    base = url.rstrip('/')
                    href = base + ("" if href.startswith("/") else "/") + href
                if save_job(title, name, href, name):
                    saved += 1
                    time.sleep(0.1)
    except Exception as e: print(f"{name} error: {e}")

if __name__ == "__main__":
    # We won't delete existing external jobs this time to accumulate them if needed, 
    # but the user said "just add the jobs", so maybe they want a fresh start.
    # Actually, let's keep it for now.
    scrape_site("https://merojob.com", "Merojob", r"/job/")
    scrape_site("https://www.jobsnepal.com", "JobsNepal", r"/job/")
    scrape_site("https://www.kumarijob.com", "KumariJob", r"job-detail")
    cursor.close()
    db.close()
    print("Done.")
