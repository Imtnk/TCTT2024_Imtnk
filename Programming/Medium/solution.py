import hashlib # import hashlib module

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

# encrypted_message = xor_with_key(message, key) # we don't need to encrypt the message

def generate_md5_hash(text):
    md5_hash = hashlib.md5(text.encode()).hexdigest()
    return md5_hash

# main starts here, we can copy message and key from the comment below
message = "🤲🤏🤔🤛🤂🤄🤞🤁🤒🥗🤸🤥🥗🤑🤘🤅🥗🤑🤂🤙🤙🤎"
keys = []
# read content from emoji_wordlist.txt seperated by new line
with open("emoji_wordlist.txt", "r", encoding="utf-8") as file:
    keys = [line.strip() for line in file]
    
# go through all the possible keys and decrypt the message
# we stop when we find the keyword "funny"
for key in keys:
    decrypted_message = xor_with_key(message, key)
    if("funny" in decrypted_message):
        print(f"Found Key: {key} -> Result: {decrypted_message}") # this line can be omited
        # now we have to generate md5 hash from the decrypted message
        md5_result = generate_md5_hash(decrypted_message)
        flag = f"THCTT24{{{md5_result}}}" # format the flag
        print(f"Flag found: {flag}") # this line is the flag
        break

# Find keyword "funny"
# message = "🤲🤏🤔🤛🤂🤄🤞🤁🤒🥗🤸🤥🥗🤑🤘🤅🥗🤑🤂🤙🤙🤎"
# key = emoji_wordlist.txt