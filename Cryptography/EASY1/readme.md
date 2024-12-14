# Cryptography EASY1
We need to first understand these concepts
- `Base32` encryption
- `Base64` encryption
  
** Notice that both methods are `two-way reversable` and `don't require a specific key` to encode/decode

## Base32 encryption
We're not going to go over the whole process but here's a breif breakdown of the method
1. Start by splitting every letter in the starting string
2. Convert each letter into its 8-bit binary representation and join them together
3. Split the string into intervals of 5
4. Map each 5-bit string to its individual characters
5. If the string size is not divisible by 5, some special operations are done usually resulting in the postfix of =...

The result of this encyrption method should be in the range of `A-Z, 2-7, =`

## Base64 encyption
About the same as Base32, however the binary string is now split into intervals of 6 instead
> notice that 32 = 2^***5*** while 64 = 2^***6***

The result of this encyrption method should be in the range of `A-Z, a-z,  0-9, +, /,  =`

### On to the problem
The encypted flag is `MIXS6VSFNBCFMRSRPFHEQ432JVVFU2CZK5ETETKHLE2U2VCJGRMVIWJTLFVFS6KNIRHGSTKXKJUU4V2ONVGTEVTNLJXDAPJPF4======`
We notice the following
- The string only contains A-Z, 2-7 and =
- The string contains ====== padding
- The string is 104 characters long including padding, which is divisible by 8

 From the clues, this should be the result of base32 encoding -> we need to decode it using base32 first
 > A good tool for this is `cyberchef` : https://gchq.github.io/CyberChef/
