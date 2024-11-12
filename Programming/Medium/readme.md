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
