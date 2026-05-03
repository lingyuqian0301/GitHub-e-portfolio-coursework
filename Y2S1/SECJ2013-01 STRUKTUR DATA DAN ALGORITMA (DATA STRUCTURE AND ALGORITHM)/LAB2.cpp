//LING YU QIAN A23CS0301 SECTION 1
#include <iostream>
using namespace std;

void swap(int& x, int& y) {
    int temp = x;
    x = y;
    y = temp;
}

// Conventional Bubble Sort
void bubbleSort(int data[], int listSize) {
    int comparisons = 0;
    int swaps = 0;
    int passes = 0;
    
    for (int pass = 1; pass < listSize; pass++) {
        passes++;
        // Moves largest element to end of array
        for (int x = 0; x < listSize - pass; x++) {
            comparisons++;
            if (data[x] > data[x+1]) {
                swap(data[x], data[x+1]);
                swaps++;
            }
        }
    }
    
    cout << "\nBubble Sort Metrics:" << endl;
    cout << "Number of passes: " << passes << endl;
    cout << "Number of comparisons: " << comparisons << endl;
    cout << "Number of swaps: " << swaps << endl;
}

// Improved Bubble Sort with early termination
void iBubbleSort(int data[], int listSize) {
    int comparisons = 0;
    int swaps = 0;
    int passes = 0;
    bool sorted = false;
    
    for (int pass = 1; (pass < listSize) && !sorted; pass++) {
        passes++;
        sorted = true;  // Assume sorted
        
        for (int x = 0; x < listSize - pass; x++) {
            comparisons++;
            if (data[x] > data[x+1]) {
                swap(data[x], data[x+1]);
                swaps++;
                sorted = false;  // Signal exchange
            }
        }
    }
    
    cout << "\nImproved Bubble Sort Metrics:" << endl;
    cout << "Number of passes: " << passes << endl;
    cout << "Number of comparisons: " << comparisons << endl;
    cout << "Number of swaps: " << swaps << endl;
}

// Selection Sort
void selectionSort(int data[], int n) {
    int comparisons = 0;
    int swaps = 0;
    int passes = 0;
    
    for (int last = n-1; last >= 1; --last) {
        passes++;
        // Select largest item in the array
        int largestIndex = 0;
        
        for (int p = 1; p <= last; ++p) {
            comparisons++;
            if (data[p] > data[largestIndex]) {
                largestIndex = p;
            }
        }
        
        // Only swap if largest item isn't already at the last position
        if (largestIndex != last) {
            swap(data[largestIndex], data[last]);
            swaps++;
        }
    }
    
    cout << "\nSelection Sort Metrics:" << endl;
    cout << "Number of passes: " << passes << endl;
    cout << "Number of comparisons: " << comparisons << endl;
    cout << "Number of swaps: " << swaps << endl;
}

void insertionSort(int data[]) {
    const int SIZE = 25; // Adjust the size based on the expected array size
    int comparisons = 0;
    int swaps = 0;
    int passes = 0;

    for (int pass = 1; pass < SIZE; pass++) {
        passes++;
        for (int insertIndex = pass; insertIndex > 0 && data[insertIndex - 1] > data[insertIndex]; insertIndex--) {
            comparisons++;
            // Swap the elements
            int temp = data[insertIndex];
            data[insertIndex] = data[insertIndex - 1];
            data[insertIndex - 1] = temp;
            swaps++;
        }
    }

    cout << "\nInsertion Sort Metrics (Using Swaps):" << endl;
    cout << "Number of passes: " << passes << endl;
    cout << "Number of comparisons: " << comparisons << endl;
    cout << "Number of swaps: " << swaps << endl;
}



void printArray(int arr[], int size) {
    for (int i = 0; i < size; i++)
        cout << arr[i] << " ";
    cout << endl;
}

void copyArray(int source[], int dest[], int size) {
    for (int i = 0; i < size; i++)
        dest[i] = source[i];
}

int main() {
    const int SIZE = 25;
    
    // Initialize arrays as per lab document
    int dataArrayA[25] = {100,50,88,30,60,45,25,12,10,5,98,15,65,55,45,70,20,90,66,22,120,48,35,85,5};
    int dataArrayB[25] = {5,8,30,25,35,40,42,50,55,22,24,66,70,75,78,80,85,90,100,118,98,120,122,121,121};
    int tempArray[SIZE];
    
    cout << "Testing Worst Case (dataArrayA - totally unsorted):" << endl;
    cout << "Original array: ";
    printArray(dataArrayA, SIZE);
    
    // Test each sorting algorithm on worst case
    copyArray(dataArrayA, tempArray, SIZE);
    bubbleSort(tempArray, SIZE);
    
    copyArray(dataArrayA, tempArray, SIZE);
    iBubbleSort(tempArray, SIZE);
    
    copyArray(dataArrayA, tempArray, SIZE);
    selectionSort(tempArray, SIZE);
    
    copyArray(dataArrayA, tempArray, SIZE);
    insertionSort(tempArray);
    
    cout << "\nTesting Best Case (dataArrayB - almost sorted):" << endl;
    cout << "Original array: ";
    printArray(dataArrayB, SIZE);
    
    // Test each sorting algorithm on best case
    copyArray(dataArrayB, tempArray, SIZE);
    bubbleSort(tempArray, SIZE);
    
    copyArray(dataArrayB, tempArray, SIZE);
    iBubbleSort(tempArray, SIZE);
    
    copyArray(dataArrayB, tempArray, SIZE);
    selectionSort(tempArray, SIZE);
    
    copyArray(dataArrayB, tempArray, SIZE);
    insertionSort(tempArray);
    
    return 0;
}