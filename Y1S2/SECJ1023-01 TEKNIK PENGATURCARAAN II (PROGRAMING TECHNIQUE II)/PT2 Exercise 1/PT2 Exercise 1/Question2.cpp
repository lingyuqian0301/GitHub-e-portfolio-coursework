// Exercise 1
// Programming Technique II
// Semester 2, 2023/2024
// Question number: 2
// Date: 13/4/2024
// Section: 7
// Member 1's Name: Wong Jia Xuan
// Member 2's Name: Cheng Jia Yi

#include <iostream>
using namespace std;

class fraction{
    private: 
    int num;
    int denom;

    public:
    fraction(){
        num = 0;
        denom = 1;
    }

    fraction(int n, int d){
        num = n;
        denom = d;
    }

    void setNum(int n){
        num = n;
    }

    void setDenom(int d){
        denom = d;
    }

    fraction multiply(const fraction other){
        int newNum = num *other.num;
        int newDenom = denom *other.denom;
        return fraction (newNum, newDenom);
    }

    fraction divide(const fraction other){
        int newNum = num *other.denom;
        int newDenom = denom *other.num;
        return fraction (newNum, newDenom);
    }

    fraction add(const fraction other)
    {
        int newNum = (num * other.denom) + (other.num * denom);
        int newDenom = denom * other.denom;
        return fraction (newNum, newDenom);
    }

    fraction subtract(const fraction other)
    {
        int newNum = (num * other.denom) - (other.num * denom);
        int newDenom = denom * other.denom;
        return fraction (newNum, newDenom);
    }

    void readFrac()
    {
        cout << "Enter numerator / denominator: ";
        char slash;
        cin >> num >> slash >> denom;
    }

    void displayFrac()
    {
        cout << num << "/" << denom;
    }

    int getNum()
    {
        return num;
    }

    int getDenom()
    {
        return denom;
    }
};

int main ()
{
    fraction f1, f2, f3;

    cout << "Enter 1st fraction: " << endl;
    f1.readFrac();

    cout << "Enter 2nd fraction: " << endl;
    f2.readFrac();

    f3 = f1.multiply(f2);
    cout << f1.getNum() << "/" << f1.getDenom() << " * ";
    cout << f2.getNum() << "/" << f2.getDenom() << " = ";
    f3.displayFrac();
    cout << endl;

    f3 = f1.divide(f2);
    cout << f1.getNum() << "/" << f1.getDenom() << " / ";
    cout << f2.getNum() << "/" << f2.getDenom() << " = ";
    f3.displayFrac();
    cout << endl;

    f3 = f1.add(f2);
    cout << f1.getNum() << "/" << f1.getDenom() << " + ";
    cout << f2.getNum() << "/" << f2.getDenom() << " = ";
    f3.displayFrac();
    cout << endl;

    f3 = f1.subtract(f2);
    cout << f1.getNum() << "/" << f1.getDenom() << " - ";
    cout << f2.getNum() << "/" << f2.getDenom() << " = ";
    f3.displayFrac();
    cout << endl;

    system ("pause");
    return 0;
}