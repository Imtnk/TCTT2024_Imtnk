# Programming Medium - Emoji_wordlist
We need to first understand these concepts
- XOR
- Text encryption

## XOR operation
XOR is a `bitwise` operation meaning it is done in the `bit` or `binary` level with the following truth table

<img src="https://i.imgur.com/ZCIJSer.png" alt="alt text" width="50%">

And for larger numbers, for example integer(either 32 or 64 bits in python)
the XOR calculation can be done as such

```.py
a = 10 #1010 in binary
b = 15 #1111 in binary

'''
a XOR b can be expressed as
a   1010
b   1111
XOR 0101 -> 5 in decimal
'''

print(a^b) #prints 5
```

## Understanding the given code
Now we are going to go through the given file `xor.py` to understand what we should do next
Firstly there are 4 functions that we assume is correct
- `text_to_ascii()`
    - returns an array of ascii value of given text -> can be use to do XOR operations
      
- `ascii_to_text()`
    - returns the text equivalent of the array of ascii numbers -> we can call this after doing XOR operations
      
- `xor_with_key()`
    - returns a string of text after doing XOR operations -> <ins>this is the main function to be called</ins>
    
- `generate_md5_hash()`
    - returns md5 hash of a text -> we need to use this to format the flag

Next we are going to try running the code as it is
> The following error should be thrown :

```
Traceback (most recent call last):
  File "/emoji_medium/xor_original.py", line 18, in <module>
    encrypted_message = xor_with_key(message, key)
                                     ^^^^^^^
 NameError: name 'message' is not defined
 ```
The problem here is the parameters that we use to call `xor_with_key()` is either not provided or not correct
That would be our job:
- provide the starting text `message`
  - should be ``` message = "🤲🤏🤔🤛🤂🤄🤞🤁🤒🥗🤸🤥🥗🤑🤘🤅🥗🤑🤂🤙🤙🤎" ```  as given in line #31
    
- provide the `key`
  - should be read from `emoji_wordlist.txt` as hinted in line #32
  - try reading all of the emojis as one long text -> this won't work, will be explained later with `keyword`
  - do this: from the file `emoji_wordlist.txt` we notice that every two emojis are seperated by a new line
    - We are going to read the file and keep the emojis in this format : ``` ['🥷😀','🥷😃','🥷😄',...]```
    - And then we will go through each pair and use them as the key until `keyword` is found

- provide the `keyword`
  - This is not required to call any of the functions ***BUT*** as we change the key, a new flag is found
  - Then how do we know if the flag is correct?
    - As hinted in line #30 of the original file, there's a mention of using the `keyword "funny"`
    - Let's try to find the keyword in the flags before hashing them to md5 -> if the keyword is found then the flag should be correct


### After making the modifications, the code should look something like this:
``` .py
import hashlib

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
```

Running the code should give us the following:
```
Found Key: 🥷🥷 -> Result: Exclusive OR for funny
Flag found: THCTT24{991968f75cd42d5a623fff107354df22}
```
Therefore our flag for this problem is `THCTT24{991968f75cd42d5a623fff107354df22}`
  
