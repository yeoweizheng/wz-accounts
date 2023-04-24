#!/usr/bin/python3
import json

def get_exchange_rate(rates, from_currency, to_currency):
    for k, v in rates.items():
        if to_currency == k:
            to_rate = v['value']
        if from_currency == k:
            from_rate = v['value']
    return to_rate / from_rate

base_currency = 'SGD'
balances = {}

with open('balances.txt') as infile:
    for row in infile.readlines():
        currency = row.split(':')[0]
        amount = float(row.split(' ')[1].rstrip('\n'))
        balances[currency] = amount
        print(row.rstrip('\n'))

with open('rates.json') as infile:
    rates = json.load(infile)['data']

amount_in_base_currency = 0
for currency, amount in balances.items():
    amount_in_base_currency += amount * get_exchange_rate(rates, currency, base_currency)

print(f'Overall {base_currency}: {amount_in_base_currency:.2f}')
