import nltk
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize
from nltk.stem import PorterStemmer
from rake_nltk import Rake

def extract_keywords(text):
  """Extracts keywords from a product description.

  Args:
    text: The product description text.

  Returns:
    A list of extracted keywords.
  """

  # Preprocess the text
  stop_words = set(stopwords.words('english'))
  words = word_tokenize(text.lower())
  filtered_words = [word for word in words if word not in stop_words]
  stemmer = PorterStemmer()
  stemmed_words = [stemmer.stem(word) for word in filtered_words]

  # Keyword extraction using RAKE
  rake = Rake()
  rake_keywords = rake.extract_keywords_from_text(text)

  # Combine keywords from both methods
  keywords = [keyword[0] for keyword in rake_keywords]
  keywords.extend(stemmed_words)

def is_duplicate(consolidated_list_of_ids, check_id):
    #pdb.set_trace()
    duplicate = False
    if(len(consolidated_list_of_ids) > 0):
        for list_product_ids in consolidated_list_of_ids:
            if(list_product_ids == check_id):
                duplicate = True
    
    return duplicate