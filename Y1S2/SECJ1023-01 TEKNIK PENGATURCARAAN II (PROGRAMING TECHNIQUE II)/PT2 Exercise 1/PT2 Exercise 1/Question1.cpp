// Exercise 1
// Programming Technique II
// Semester 2, 2023/2024
// Question number: 1
// Date: 13/4/2024
// Section: 7
// Member 1's Name: Wong Jia Xuan
// Member 2's Name: Cheng Jia Yi

#include <iostream>
#include <cmath>
using namespace std;

const double PI = 3.142;

class Cone {
    private: 
    double radius;
    double height;

    public: 
    double baseArea;
    double volume;

    void readData(){
        cout << "Enter the radius of the cone: ";
        cin >> radius;
        cout << "Enter the height of the cone:";
        cin >> height;
    }

    void calculateBaseArea(){
        baseArea = PI * radius * radius;
    }

    void calculateVolume(){
        volume = (1.0 / 3.0) * baseArea * height;
    }

    void displayData(){
        cout << "Radius: " << radius << endl;
        cout << "Height: " << height << endl;
        cout << "Base Area: " << baseArea << endl;
        cout << "Volume: " << volume << endl;
    }

};

int main()
{
    Cone cn1;
    cn1.readData();
    cn1.calculateBaseArea();
    cn1.calculateVolume();
    cn1.displayData();

    system ("pause");
    return 0;
}