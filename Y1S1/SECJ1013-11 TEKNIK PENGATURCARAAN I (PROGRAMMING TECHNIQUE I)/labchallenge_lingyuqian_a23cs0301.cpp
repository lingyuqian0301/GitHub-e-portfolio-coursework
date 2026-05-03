#include<iostream>
using namespace std;

int main ()
{
    int num, sum, remainder;

    cout << "Enter an integer number : ";
    cin >> num;

    do
    {
         remainder=num%10;
         sum=sum+remainder;
         cout << remainder;
         num=num/10;
         if (num>0)
            cout << "+";
        else
            cout << "=";         
    } while (num>0);
    cout << sum;

    if (sum%3==0)
        cout << "\n"<< sum << " is a multiple of 3";
    else if (sum%3==1)
        cout << "\n" << sum << "is not a multiple of 3\n"<< "Its remainder is 1";
    else 
        cout << "\n" << sum << "is not a multiple of 3\n"<< "Its remainder is 2"; 
    return 0;
}