TITLE lab3(PART1 : Interactive Usage of Link Libraries) (program 1)
; Author: Koo Xuan &Ling Yu Qian
; Date: 28 June 2024

include Irvine32.inc
.data
message1 BYTE "Calculate Perimeter 2-Hexagon (LOOP and ADD instructions) : ", 0
message2 BYTE "Input Hexagon 1 (side length) : ", 0
message3 BYTE "Input Hexagon 2 (side length) : ", 0
sideHex1 DWORD ?
sideHex2 DWORD ?
message4 BYTE "Result of Perimeter Hexagon 1 and 2 : ", 0
Perimeter_hexagon1 DWORD ?
Perimeter_hexagon2 DWORD ?
TotalPerimeter DWORD ?
message5 BYTE "Total Perimeter Hexagon 1 and 2 : ", 0

.code
main proc
startProg :
call Clrscr
mov edx, offset message1
call WriteString
call crlf
call crlf

mov edx, offset message2
call WriteString
; read input sideHex1
call ReadDec
mov sideHex1, eax

mov edx, offset message3
call WriteString
; read input sideHex2
call ReadDec
mov sideHex2, eax
call Crlf

; Calculate the perimeter of the Hexagon1
mov ecx, 6
mov eax, sideHex1
mov ebx, 0
calcPerimeter1:
add ebx, eax
loop calcperimeter1
mov Perimeter_hexagon1, ebx

; Calculate the perimeter of the Hexagon2
mov ecx, 6
mov eax, sideHex2
mov ebx, 0
calcPerimeter2:
add ebx, eax
loop calcperimeter2
mov Perimeter_hexagon2, ebx

; Display result hex1 & 2
mov edx, offset message4
call WriteString
call Crlf
mov eax, Perimeter_hexagon1
call WriteDec
call Crlf
mov eax, Perimeter_hexagon2
call WriteDec
call Crlf
call Crlf

; Calculate the total perimeter
mov eax, Perimeter_hexagon1
add eax, Perimeter_hexagon2
mov TotalPerimeter, eax

; Display the total perimeter
mov edx, offset message5
call WriteString
mov eax, TotalPerimeter
call WriteDec
call Crlf

exit
main ENDP

END main