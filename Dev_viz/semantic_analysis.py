# semantic_analysis.py
import sys
import json
import numpy as np
import torch
from torchtext.vocab import GloVe
from sklearn.manifold import TSNE


def calculate_tsne(data):
    glove = GloVe(name="6B", dim=300)
    word_vectors = []

    for item in data:
        word = item["word"]
        try:
            word_vectors.append(glove.vectors[glove.stoi[word]])
        except KeyError:
            pass

    if not word_vectors:
        return []

    word_vectors = torch.stack(word_vectors)
    tsne = TSNE(n_components=2)
    word_vectors_2d = tsne.fit_transform(word_vectors)

    return [
        {
            "word": data[i]["word"],
            "fd": data[i]["fd"],
            "frequency": data[i]["frequency"],
            "position": word_vectors_2d[i].tolist(),
        }
        for i in range(len(data))
    ]


if __name__ == "__main__":
    input_data = json.loads(sys.argv[1])
    result = calculate_tsne(input_data)
    print(json.dumps(result))
