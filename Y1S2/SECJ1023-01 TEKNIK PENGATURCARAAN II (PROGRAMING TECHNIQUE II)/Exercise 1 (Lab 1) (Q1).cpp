// Exercise 1: Introduction to Classes and Objects
// Programming Technique II
// Semester 2, 2023/2024

// Date : 04/05/2024
// Section : 01
// Member 1's Name : Ling Yu Qian (A23CS0301)
// Member 2's Name : Koo Xuan (A23CS0300)

#include <iostream>
#include<cmath>
using namespace std;

const double PI=3.14159;

class Cone{
    private:
    double radius;
    double height;
    double baseArea;
    double volume;

    public:
    Cone():radius(0),height(0),baseArea(0),volume(0){
    }
    void readData(){
        cout<<"Enter radius:";
        cin>>radius;
        cout<<"Enter height :";
        cin>>height;    }
    void calcVolume(){
        volume=(1.0/3.0)*baseArea*height;
    }
    void calcBaseArea(){
        baseArea=PI*radius*radius;
    }
    void displayData(){
        cout<<"radius:"<<radius<<endl;
        cout<<"height:"<<height<<endl;
        cout<<"base area:"<<baseArea<<endl;
        cout<<"volume:"<<volume<<endl;
    }
   
};
 int main(){
    Cone c1;
    c1.readData();
    c1.calcBaseArea();
    c1.calcVolume();
    c1.displayData();
    return 0;
 }