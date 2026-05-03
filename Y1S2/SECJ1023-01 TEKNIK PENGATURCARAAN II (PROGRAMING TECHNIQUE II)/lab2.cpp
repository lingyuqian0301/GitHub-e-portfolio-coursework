#include <iostream>
using namespace std;

void bubbleSort(int data[], int listSize) {
    int comparisons = 0, swaps = 0, passes = 0;
    for (int pass = 1; pass < listSize; pass++) {
        passes++;
        for (int x = 0; x < listSize - pass; x++) {
            comparisons++;
            if (data[x] > data[x + 1]) {
                int value = data[x];
                data[x] = data[x + 1];
                data[x + 1] = value;
                swaps++;
            }
        }
    }
    cout << "Bubble Sort: Passes=" << passes << ", Comparisons=" << comparisons << ", Swaps=" << swaps << endl;
}

void iBubbleSort(int data[], int listSize) {
    int comparisons = 0, swaps = 0, passes = 0;
    bool sorted = false;
    for (int pass = 1; (pass < listSize) && !sorted; ++pass) {
        passes++;
        sorted = true;
        for (int x = 0; x < listSize - pass; ++x) {
            comparisons++;
            if (data[x] > data[x + 1]) {
                int value = data[x];
                data[x] = data[x + 1];
                data[x + 1] = value;
                swaps++;
                sorted = false;
            }
        }
    }
    cout << "Improved Bubble Sort: Passes=" << passes << ", Comparisons=" << comparisons << ", Swaps=" << swaps << endl;
}

void selectionSort(int data[], int n) {
    int comparisons = 0, swaps = 0, passes = 0;
    for (int last = n - 1; last >= 1; --last) {
        passes++;
        int largestIndex = 0;
        for (int p = 1; p <= last; ++p) {
            comparisons++;
            if (data[p] > data[largestIndex])
                largestIndex = p;
        }
        if (largestIndex != last) {
            swap(data[largestIndex], data[last]);
            swaps++;
        }
    }
    cout << "Selection Sort: Passes=" << passes << ", Comparisons=" << comparisons << ", Swaps=" << swaps << endl;
}

void swap(int& x, int& y) {
    int value = x;
    x = y;
    y = value;
} // end swap


void insertionSort(int data[], int n) {
    int comparisons = 0, swaps = 0, passes = 0;
    for (int pass = 1; pass < n; pass++) {
        passes++;
        int x = data[pass];
        int insertIndex = pass;
        while ((insertIndex > 0) && (data[insertIndex - 1] > x)) {
            comparisons++;
            data[insertIndex] = data[insertIndex - 1];
            insertIndex--;
        }
        comparisons++; // For the last failed comparison
        data[insertIndex] = x;
        if (insertIndex != pass) swaps++;
    }
    cout << "Insertion Sort: Passes=" << passes << ", Comparisons=" << comparisons << ", Swaps=" << swaps << endl;
}

void printArray(const int data[], int n) {
    for (int i = 0; i < n; i++) {
        cout << data[i] << " ";
    }
    cout << endl;
}

int main() {
    int dataArrayA[25] = {100, 50, 88, 30, 60, 45, 25, 12, 10, 5, 98, 15, 65, 55, 45, 70, 20, 90, 66, 22, 120, 48, 35, 85, 5};
    int dataArrayB[25] = {5, 8, 30, 25, 35, 40, 42, 50, 55, 22, 24, 66, 70, 75, 78, 80, 85, 90, 100, 118, 98, 120, 122, 121, 121};

    int n = 25;

    cout << "Testing Sorting with dataArrayA:" << endl;

    bubbleSort(dataArrayA, n);
    cout << "Sorted Array (Bubble Sort): ";
    printArray(dataArrayA, n);

    iBubbleSort(dataArrayA, n);
    cout << "Sorted Array (Improved Bubble Sort): ";
    printArray(dataArrayA, n);

    selectionSort(dataArrayA, n);
    cout << "Sorted Array (Selection Sort): ";
    printArray(dataArrayA, n);

    insertionSort(dataArrayA, n);
    cout << "Sorted Array (Insertion Sort): ";
    printArray(dataArrayA, n);

    cout << endl;

    cout << "Testing Sorting with dataArrayB:" << endl;

    bubbleSort(dataArrayB, n);
    cout << "Sorted Array (Bubble Sort): ";
    printArray(dataArrayB, n);

    iBubbleSort(dataArrayB, n);
    cout << "Sorted Array (Improved Bubble Sort): ";
    printArray(dataArrayB, n);

    selectionSort(dataArrayB, n);
    cout << "Sorted Array (Selection Sort): ";
    printArray(dataArrayB, n);

    insertionSort(dataArrayB, n);
    cout << "Sorted Array (Insertion Sort): ";
    printArray(dataArrayB, n);

    return 0;
}

