//LING YU QIAN A23CS0301 16/11/2023
#include<iostream>
using namespace std;

int main (){
	   int numLines;
       cout << "Enter the number of lines: ";
       cin >> numLines;
        for (int i = numLines; i >= 1; --i) {
        for (int j = i; j >= 1; --j) {
            cout << j << " ";
        }
        cout << endl;
    }
	return 0;
}