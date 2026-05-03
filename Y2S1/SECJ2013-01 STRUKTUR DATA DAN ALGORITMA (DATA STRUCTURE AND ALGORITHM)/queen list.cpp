#include <iostream>  
using namespace std; 

// Define the structure for a queue node  
struct nodeQ {  
    int item;         // Data stored in the node
    nodeQ* next;      // Pointer to the next node
};  

// Define the Queue class  
class Queue {  
public:  
    nodeQ* frontPtr;  // Pointer to the front of the queue
    nodeQ* backPtr;   // Pointer to the back of the queue  

    // Constructor to initialize the queue  
    Queue() {  
        frontPtr = NULL;  
        backPtr = NULL;  
    }  

    // Function to check if the queue is empty  
    bool isEmpty() {  
        return frontPtr == NULL;  
    }  

    // Function to enqueue an element  
    void enQueue(int value) {  
        nodeQ* newPtr = new nodeQ;  
        newPtr->item = value;       
        newPtr->next = NULL;  
		
        if (isEmpty()) {            
            // If empty, set both front and back pointers to the new node  
            frontPtr = newPtr;  
            backPtr = newPtr;  
        } else {                    
            // Link the new node to the back of the queue  
            backPtr->next = newPtr;  
            // Update the back pointer to the new node  
            backPtr = newPtr;  
        }        
    }  

    // Function to display the queue  
    void displayQueue() {  
        nodeQ* current = frontPtr;   
        while (current != NULL) {     
            cout << current->item << " ";  // Print the item of the current node  
            current = current->next;      // Move to the next node  
        }  
        cout << endl;    
    }  

    // Destructor to clean up the queue  
    ~Queue() {  
        destroyQueue();  // Call a function to destroy the queue  
    }  

    // Function to destroy all nodes in the queue  
    void destroyQueue() {  
        nodeQ* temp = frontPtr; 
        while (temp) {           
            frontPtr = frontPtr->next;  // Move the front pointer to the next node  
            delete temp;                // Delete the current node  
            temp = frontPtr;            // Move to the next node  
        }  
        backPtr = NULL;  // Set the back pointer to NULL 
    }  
};  

int main() {  

    Queue myQueue;   

    myQueue.enQueue(20); 
    myQueue.enQueue(15); 
    myQueue.enQueue(26);  
    myQueue.enQueue(84); 
 
    cout << "Queue elements: ";  
    myQueue.displayQueue();  

    cout << "Is the queue empty? " << (myQueue.isEmpty() ? "Yes" : "No") << endl;  

    return 0;   
}
