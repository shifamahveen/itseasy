import sys
import json
import re
from pdfminer.high_level import extract_text

def parse_resume(file_path):
    try:
        # Extract text from the resume file
        text = extract_text(file_path)

        # Initialize parsed data with empty fields
        parsed_data = {
            "name": "",
            "email": "",
            "phone": "",
            "college": "",
            "branch": "",
            "year_of_passing": "",
            "gender": "",
            "current_city": "",
            "state": "",
            "class_10_percentage": "",
            "class_12_percentage": "",
            "graduation_percentage": "",
            "backlogs": "",
        }

        # Regex patterns to extract data
        email_pattern = r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}'
        phone_pattern = r'\+?\d{10,15}'
        percentage_pattern = r'\b\d{1,2}(\.\d{1,2})?\b'  # Matches numbers like 85, 89.5
        year_pattern = r'\b(19|20)\d{2}\b'

        # Parsing logic
        if "Name:" in text:
            parsed_data["name"] = text.split("Name:")[1].split("\n")[0].strip()
        else:
            name_match = re.search(r'(?:Name|Full Name):\s*(.+)', text, re.IGNORECASE)
            if name_match:
                parsed_data["name"] = name_match.group(1).strip()

        email_match = re.search(email_pattern, text)
        if email_match:
            parsed_data["email"] = email_match.group(0)

        phone_match = re.search(phone_pattern, text)
        if phone_match:
            parsed_data["phone"] = phone_match.group(0)

        if "College:" in text:
            parsed_data["college"] = text.split("College:")[1].split("\n")[0].strip()

        if "Branch:" in text:
            parsed_data["branch"] = text.split("Branch:")[1].split("\n")[0].strip()

        year_match = re.search(year_pattern, text)
        if year_match:
            parsed_data["year_of_passing"] = year_match.group(0)

        if "Gender:" in text:
            parsed_data["gender"] = text.split("Gender:")[1].split("\n")[0].strip()

        if "City:" in text:
            parsed_data["current_city"] = text.split("City:")[1].split("\n")[0].strip()

        if "State:" in text:
            parsed_data["state"] = text.split("State:")[1].split("\n")[0].strip()

        percentages = re.findall(percentage_pattern, text)
        if len(percentages) >= 1:
            parsed_data["class_10_percentage"] = percentages[0]
        if len(percentages) >= 2:
            parsed_data["class_12_percentage"] = percentages[1]
        if len(percentages) >= 3:
            parsed_data["graduation_percentage"] = percentages[2]

        if "Backlogs:" in text:
            parsed_data["backlogs"] = text.split("Backlogs:")[1].split("\n")[0].strip()

        return parsed_data
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    file_path = sys.argv[1]
    parsed_data = parse_resume(file_path)
    print(json.dumps(parsed_data))
