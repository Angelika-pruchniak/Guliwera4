# Druga opinia Gemini: python3 narzedzia/gemini.py MODEL PLIK_Z_BRIEFEM
# Klucz API wstrzykuje proxy środowiska (sekret sieciowy), w kodzie jest tylko atrapa "proxy-injected".
import json,sys,urllib.request,urllib.error
model,src=sys.argv[1],sys.argv[2]
b=open(src,encoding='utf-8').read()
req=urllib.request.Request('https://generativelanguage.googleapis.com/v1beta/openai/chat/completions',data=json.dumps({"model":model,"messages":[{"role":"user","content":b}]}).encode(),headers={"Content-Type":"application/json","Authorization":"Bearer proxy-injected"})
try:
    r=json.load(urllib.request.urlopen(req,timeout=300))
    print(r['choices'][0]['message']['content']); print('\n[model:',r.get('model'),']')
except urllib.error.HTTPError as e:
    print('ERR',e.code,e.read().decode()[:600]); sys.exit(1)
