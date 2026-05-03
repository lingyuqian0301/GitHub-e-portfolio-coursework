; Lab3; Authors: LING YU QIAN, KOO XUAN
; Date: 21 June 2024
INCLUDE Irvine32.inc

.data
str6 BYTE "Calculate SUM (unsign INT) index (Odd or Even) in array Hello[6] : ", 0dh, 0ah, 0
str7 BYTE "Interger Input : ", 0
hello DWORD 6 DUP(0)
str9 BYTE "Result Sum Hello[index]:", 0dh, 0ah, 0
str10 BYTE "Sum Hello[even] index location : ", 0
str11 BYTE "Sum Hello[odd] index location : ", 0
TotalEVEN DWORD 0
TotalODD DWORD 0

.code
main PROC
; Display initial message
mov edx, OFFSET str6
call WriteString
call Crlf; Blank line after str6

; Input values into the array
mov esi, OFFSET hello
mov ecx, 6; Loop counter for 6 values
xor ebx, ebx; Index

input_values :
; Display "Interger Input : " prompt
mov edx, OFFSET str7
call WriteString
; Read integer input
call ReadDec
mov[esi + ebx * 4], eax

; Increment indexand check loop condition
inc ebx
loop input_values

; Calculate sum of evenand odd index locations
mov ecx, 6
xor ebx, ebx; Reset index

count_even_odd :
mov eax, ebx
and eax, 1
jz even_index

odd_index :
mov eax, [hello + ebx * 4]
add TotalODD, eax
jmp continue_loop

even_index :
mov eax, [hello + ebx * 4]
add TotalEVEN, eax

continue_loop :
inc ebx
loop count_even_odd

; Display result message
call Crlf;
mov edx, OFFSET str9
call WriteString
call Crlf

; Display the results
mov edx, OFFSET str10
call WriteString
mov eax, TotalEVEN
call WriteDec

call Crlf; Move cursor to next line after the number

mov edx, OFFSET str11
call WriteString
mov eax, TotalODD
call WriteDec

; Move cursor to next line after the number
call Crlf

; End the program
exit
main ENDP
END main