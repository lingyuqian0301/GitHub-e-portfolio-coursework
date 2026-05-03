// Exercise 1: Introduction to Classes and Objects
// Programming Technique II
// Semester 2, 2023/2024

// Date : 04/05/2024
// Section : 01
// Member 1's Name : Ling Yu Qian (A23CS0301)
// Member 2's Name : Koo Xuan (A23CS0300)

#include <iostream>
using namespace std;

class Fraction
{
    private:
        int numerator;
        int denominator;

    public:
        Fraction() : numerator(0), denominator(1){}
        Fraction(int num, int denom) : numerator(num), denominator(denom) {}
        Fraction add(const Fraction& other) const{
            int newNumerator = (numerator * other.denominator) + (other.numerator * denominator);
            int newDenominator = denominator * other.denominator;
            return Fraction(newNumerator, newDenominator);
        }
        Fraction subtract(const Fraction& other) const{
            int newNumerator = (numerator * other.denominator) - (other.numerator * denominator);
            int newDenominator = denominator * other.denominator;
            return Fraction(newNumerator, newDenominator);
        }
        Fraction multiply(const Fraction& other) const{
            int newNumerator = numerator * other.numerator;
            int newDenominator = denominator * other.denominator;
            return Fraction(newNumerator, newDenominator);
        }
        Fraction divide(const Fraction& other) const{
            int newNumerator = numerator * other.denominator;
            int newDenominator = denominator * other.numerator;
            return Fraction(newNumerator, newDenominator);
        }
        void readFrac(){
            char slash;
            cout << "Enter numerator / denominator: ";
            cin >> numerator >> slash >> denominator;
        }
        void displayFrac(){
            cout << numerator << "/" << denominator;
        }
};

int main()
{
    Fraction f1, f2, f3;
    cout << "Enter 1st fraction: " << endl;
    f1.readFrac();
    cout << "Enter 2nd fraction: " << endl;
    f2.readFrac(); 

    f3 = f1.multiply(f2);
    f1.displayFrac();
    cout << " * ";
    f2.displayFrac();
    cout << " = ";
    f3.displayFrac();
    cout << endl;

    f3 = f1.divide(f2);
    f1.displayFrac();
    cout << " / ";
    f2.displayFrac();
    cout << " = ";
    f3.displayFrac();
    cout << endl;

    f3 = f1.add(f2);
    f1.displayFrac();
    cout << " + ";
    f2.displayFrac();
    cout << " = ";
    f3.displayFrac();
    cout << endl;

    f3 = f1.subtract(f2);
    f1.displayFrac();
    cout << " - ";
    f2.displayFrac();
    cout << " = ";
    f3.displayFrac();
    cout << endl;

    return 0;
}