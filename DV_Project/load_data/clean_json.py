import json
null = None
# Reference : http://stackoverflow.com/questions/29947162/how-to-clean-a-json-file-and-store-it-to-another-file-in-python
class Amazon():
    def parse(self, inpath, outpath):
        g = open(inpath, 'r')
        with open(outpath, 'w') as fout:
            for l in g:
                fout.write(json.dumps(eval(l)))
original_json = ["webmd-topics","webmd-related_topic","webmd-question","webmd-member","webmd-answer"]
amazon = Amazon()

for original in original_json:
    amazon.parse("../data/json/"+original+".json", "../data/clean_json/"+original+"-cleaned.json")