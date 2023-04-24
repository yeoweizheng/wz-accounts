#!/usr/bin/python3
import json
import requests

API_KEY = ''
API_URL = f'https://api.currencyapi.com/v3/latest?apikey={API_KEY}'

response = requests.get(API_URL).json()

with open('rates.json', 'w+') as outfile:
    json.dump(response, outfile, indent=2)

print('Latest rates retrieved.')
