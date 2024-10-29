flag = ""

def text_to_ascii(text):
    return [ord(char) for char in text]

def ascii_to_text(ascii_list):
    return ''.join(chr(num) for num in ascii_list)

def xor_with_key(text, key):
    text_ascii = text_to_ascii(text)
    key_ascii = text_to_ascii(key)
    key_length = len(key_ascii)

    # ทำการ XOR แต่ละตัวอักษรของข้อความกับคีย์ที่วนซ้ำ
    result_ascii = [(text_ascii[i] ^ key_ascii[i % key_length]) for i in range(len(text_ascii))]
    return ascii_to_text(result_ascii)

encrypted_message = xor_with_key(message, key)

def generate_md5_hash(text):
    md5_hash = hashlib.md5(text.encode()).hexdigest()
    return md5_hash

decrypted_message = xor_with_key(message, key)
print(f"Found Key: {key} -> Result: {decrypted_message}")
md5_result = generate_md5_hash(decrypted_message)
flag = f"THCTT24{{{md5_result}}}"
print(f"Flag found: {flag}")

# Find keyword "funny"
# message = "🤲🤏🤔🤛🤂🤄🤞🤁🤒🥗🤸🤥🥗🤑🤘🤅🥗🤑🤂🤙🤙🤎"
# key = emoji_wordlist.txt