""" Parse OCR data using Aadhaar card Image."""

import re
import dateutil.parser as dparser

DOB_FORMAT = "%Y-%m-%d"
DOB_SEARCH_TEXT = 'Year|Birth|irth|YoB|YOB:|DOB:|DOB'
ADDRESS_REGEX_3 = 'Address(.*\s\d{6})'
ADDRESS_REGEX_4 = '(S\/O|W\/O|D\/O|C\/O)(.*\s\d{6})'
GENDERSTR_REGEX = '(Female|Male|emale|male|ale|FEMALE|MALE|EMALE)$'
UID_REGEX = '(\d{4} \d{4} \d{4})'
DOB_REGEX = '(\d{2}\/\d{2}\/\d{4})'


class AadhaarCardParser:
    
    def __init__(self, image_text, params={}):
        self.image_text = image_text
        self.params = params

    def get_dob(self):
        matches = re.findall(DOB_REGEX, self.image_text)
        dob_str = '1990-01-01'
        if matches:
            dob = matches[0]
            dob_str = dparser.parse(dob, fuzzy=True, dayfirst=True).strftime(DOB_FORMAT)
        return dob_str

    def get_ui(self):
        matches = re.findall(UID_REGEX, self.image_text)
        uid = ""
        if matches:
            uid = matches[0].replace(" ", "")
        return uid

    def get_gender(self):
        gender = "NA"
        genline = []
        try:
            for wordlist in self.image_text.split('\n'):
                xx = wordlist.split()
                if [w for w in xx if re.search(GENDERSTR_REGEX, w)]:
                    genline = wordlist
                    break
            if 'Female' in genline or 'FEMALE' in genline:
                gender = "Female"
            if 'Male' in genline or 'MALE' in genline:
                gender = "Male"
        except Exception:
            pass
        return gender

    def get_yearline(self):
        yearline = ""
        text1 = []
        for wordlist in self.image_text.split('\n'):
            xx = wordlist.split()
            if [w for w in xx if re.search(f'({DOB_SEARCH_TEXT})$', w)]:
                yearline = wordlist
                break
            else:
                text1.append(wordlist)
        return yearline

    def get_parsed_text(self, yearline):
        parsed_text_list = ""
        try:
            parsed_text_list = self.image_text.split(yearline, 1)[1]
        except Exception:
            pass
        return parsed_text_list

    def get_name(self):
        name = ""
        NAME_REGEX = r"^[a-zA-Z]{4}[a-zA-Z\s]{3,20}$"
        text_list = []
        exclude_word_list = [
            'Government of India',
            'AADHAAR',
        ]
        for wordlist in self.image_text.split('\n'):
            # replace numeric values from text.
            for num in range(0, 10):
                wordlist = wordlist.replace(str(num), '')
            wordlist = wordlist.strip()
            matches = re.findall(NAME_REGEX, wordlist)
            for each_match in matches.copy():
                if each_match in exclude_word_list:
                    matches.remove(each_match)
            text_list.extend(matches)
        if text_list:
            name = text_list[-1]
            name = name.strip()
        return name

    def get_address(self):
        address = ""
        matches = re.findall(re.compile(ADDRESS_REGEX_3), self.image_text.replace('\n', '\\n'))
        if matches:
            if '' in matches:
                matches.remove('')
            add_matches = re.findall(re.compile(ADDRESS_REGEX_4), matches[0])
            address = "\n".join(matches)
            if add_matches:
                address = " ".join(add_matches[0])
            address = address.replace('\\n', '\n')
            cleanup_subsstring = [
                '\ni ',
                '\n\n',
                '8/0',
                ':\n8/0',
                ':\\n'
            ]
            for each_substring in cleanup_subsstring:
                if address.startswith(each_substring):
                    address = address.replace(each_substring, '')
        return self.get_address_json(address.strip(), matches)

    def get_address_json(self, address, matches):

        if len(matches) < 1:
            return {
                'address_line_1': 'NA',
                'address_line_2': 'NA',
                'station': 'NA',
                'state': 'NA',
                'pin': 'NA',
                'country': 'India'
            }
        PIN = None
        LINE1 = None
        LINE2 = None
        STATE = None
        STATION = None
        address = address.replace(',', '')
        ADDRESS_DICT = address.split('\n')
        PIN_CODE_CHECK_REGEX = '\d{6}'
        pin_matches = re.findall(re.compile(PIN_CODE_CHECK_REGEX), address)
        if len(pin_matches) > 0:
            PIN = pin_matches[-1]

        if len(ADDRESS_DICT) > 0:
            address_state_dict = ADDRESS_DICT[-1].split(' ')
            if address_state_dict:
                STATE = address_state_dict[0]

        if len(ADDRESS_DICT) > 1:
            address_station_dict = ADDRESS_DICT[-2].split(' ')
            if address_station_dict:
                STATION = address_station_dict[-1]

        if STATE and STATION and PIN:
            LINE2 = ADDRESS_DICT[-3]
            LINE1 = []
            for index in range(0, (len(ADDRESS_DICT)-2)):
                LINE1.append(f'{ADDRESS_DICT[index]}')
            if LINE1:
                TEMP_LINE1 = ", ".join(LINE1)
                LINE1 = TEMP_LINE1
        else:
            count = 2
            if not STATE:
                STATE = 'N/A'
                count -= 1
            if not STATION:
                STATION = 'N/A'
                count -= 1
            if not PIN:
                PIN = 'N/A'
                count -= 1
            LINE1 = []
            LINE2 = ''
            for index in range(0, (len(ADDRESS_DICT)-count)):
                LINE1.append(f'{ADDRESS_DICT[index]}')
            if LINE1:
                TEMP_LINE1_ADDRESS = []
                TEMP_LINE2_ADDRESS = []
                for index in range(0, ((len(LINE1)//2)-1)):
                    TEMP_LINE1_ADDRESS.append(LINE1[index])
                for index in range(((len(LINE1)//2)), (len(LINE1)-1)):
                    TEMP_LINE2_ADDRESS.append(LINE1[index])
                LINE1 = ", ".join(TEMP_LINE1_ADDRESS)
                LINE2 = ", ".join(TEMP_LINE2_ADDRESS)
            else:
                LINE1 = LINE2 = 'N/A'

        return {
            'address_line_1': LINE1.strip(),
            'address_line_2': LINE2.strip(),
            'station': STATION.strip(),
            'state': STATE.strip(),
            'pin': PIN.strip(),
            'country': 'India'
        }

    def run_parser(self):
        date_of_birth = self.get_dob()
        gender = self.get_gender()
        uid = self.get_ui()
        name = self.get_name()
        address = self.get_address()
        data = {
            "uid": uid,
            "name": name,
            "date_of_birth": date_of_birth.strip(),
            "gender": gender,
            "address": address
        }
        return data
