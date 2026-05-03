TITLE Lab3
; Author:LING YU QIAN ,KOO XUAN
; Date: 30 June 2024

INCLUDE Irvine32.inc

.data
message1 BYTE "Welcome to simple math activities:", 0dh, 0ah, 0dh, 0ah,"Main Menu:", 0dh, 0ah, 0dh, 0ah
BYTE "1. To calculate Perimeter Hexagon (LOOP and ADD instructions)", 0dh, 0ah
BYTE "2. To calculate SUM (unsigned Int) index [Odd or Even] in an array Hello[6]", 0dh, 0ah, 0

message2 BYTE "Select Your Input: ", 0
decNumInOption DWORD ?
promptBad BYTE "Invalid input, please enter again", 0

message3 BYTE "Calculate Perimeter 2-Hexagon (LOOP and ADD instructions):", 0dh, 0ah, 0dh, 0ah
BYTE "Input Hexagon 1 (side length): ", 0
message3_1 BYTE "Input Hexagon 2 (side length): ", 0

decNumInHex1 DWORD ?
decNumInHex2 DWORD ?

message4 BYTE "Result of Perimeter Hexagon 1 and 2: ", 0dh, 0ah, 0
sumHex1 DWORD ?
sumHex2 DWORD ?
str5 BYTE "Total Perimeter Hexagon 1 and 2 : ", 0
totHex DWORD ?

message5 BYTE "Calculate SUM (unsigned Int) index [Odd or Even] in array Hello[6]: ", 0dh, 0ah, 0
strinNo BYTE "Input INT (unsigned): ", 0
hello DWORD 6 DUP(0)

int_message BYTE "Integer Input: ", 0

sum_odd DWORD ?
sum_even DWORD ?

str_result_hello BYTE "Result Sum Hello[index]: ", 0
str_result_odd BYTE "Sum Hello[odd] index location: ", 0
str_result_even BYTE "Sum Hello[even] index location: ", 0

stryn BYTE "Press 'y' to Main Menu or 'n' to Exit the benchmark: ", 0
charIn BYTE ?
charY BYTE 'y'
strbye BYTE "Thank you...BYE!!", 0dh, 0ah, 0

.code
main PROC

startProg :
call Clrscr
mov edx, OFFSET message1
call WriteString
call Crlf

mov edx, OFFSET message2
call WriteString

; read input Main Menu
read_inOption :
call ReadDec
jnc goodInOption

mov edx, OFFSET promptBad
call WriteString
jmp read_inOption; go input again

goodInOption :
mov decNumInOption, eax
call Crlf

mov ebx, 1
mov eax, decNumInOption
cmp eax, ebx
je periHex_loopAdd

mov ebx, 2
mov eax, decNumInOption
cmp eax, ebx
je calSum_oddeven

periHex_loopAdd :
call Clrscr
mov edx, OFFSET message3
call WriteString

; read input Hex1(side_length)
read_inOptionHex1:
call ReadDec
jnc goodInOptionHex1

mov edx, OFFSET promptBad
call WriteString
jmp read_inOptionHex1; go input again

goodInOptionHex1 :
mov decNumInHex1, eax

; read input Hex2(side_length)
mov edx, OFFSET message3_1
call WriteString

read_inOptionHex2 :
call ReadDec
jnc goodInOptionHex2

mov edx, OFFSET promptBad
call WriteString
jmp read_inOptionHex2; go input again

goodInOptionHex2 :
mov decNumInHex2, eax
call Crlf

mov ecx, 6
mov eax, 0
mov ebx, 0

loopAddHex:
add eax, decNumInHex1
add ebx, decNumInHex2
loop loopAddHex

mov sumHex1, eax
mov sumHex2, ebx

mov eax, sumHex1
add eax, sumHex2
mov totHex, eax

mov edx, OFFSET message4
call WriteString
mov eax, sumHex1
call WriteDec
call Crlf
mov eax, sumHex2
call WriteDec
call Crlf
call Crlf
mov edx, OFFSET str5
call WriteString
mov eax, totHex
call WriteDec
call Crlf
call Crlf

mov edx, OFFSET stryn
call WriteString

call ReadChar
mov charIn, AL
call WriteChar
call Crlf
call Crlf

mov BL, charY
cmp BL, charIn
JE startProg

mov edx, OFFSET strbye
call WriteString
exit

calSum_oddeven :
call Clrscr
mov edx, OFFSET message5
call WriteString
call Crlf

; read 6 integer numbers
mov ecx, 6
mov ebx, 0

loopL1:
mov edx, OFFSET int_message
call WriteString
call ReadDec
jnc goodInOption2

mov edx, OFFSET promptBad
call WriteString
jmp loopL1; go input again

goodInOption2 :
mov hello[ebx], eax
add ebx, 4
loop loopL1

; calculate sum of even locations in hello[]
mov ecx, 3
mov ebx, 0
mov eax, 0

loopL2:
add eax, hello[ebx]
add ebx, 8
loop loopL2
mov sum_even, eax

; calculate sum of odd locations in hello[]
call Crlf
mov ecx, 3
mov ebx, 4
mov eax, 0

loopL3:
add eax, hello[ebx]
add ebx, 8
loop loopL3
mov sum_odd, eax

; output result : sum of evenand odd indices in hello[]
mov edx, OFFSET str_result_hello
call WriteString
call Crlf
mov edx, OFFSET str_result_even
call WriteString
mov eax, sum_even
call WriteDec
call Crlf

mov edx, OFFSET str_result_odd
call WriteString
mov eax, sum_odd
call WriteDec
call Crlf
call Crlf

mov edx, OFFSET stryn
call WriteString

call ReadChar
mov charIn, AL
call WriteChar
call Crlf
call Crlf

mov BL, charY
cmp BL, charIn
JE startProg

mov edx, OFFSET strbye
call WriteString
exit

main ENDP

END main