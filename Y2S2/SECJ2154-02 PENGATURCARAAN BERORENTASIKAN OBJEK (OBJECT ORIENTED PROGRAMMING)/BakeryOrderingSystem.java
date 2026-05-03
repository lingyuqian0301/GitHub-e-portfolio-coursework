import javax.swing.*;
import java.awt.*;
import java.io.*;
import java.nio.file.*;
import java.util.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

enum FoodType { BREAD, CAKE, BISCUIT, PASTRY, BEVERAGE }
enum AllergyTag { GLUTEN_FREE, NUT_FREE, DAIRY_FREE, VEGAN }
enum OrderStatus { PENDING, PURCHASED }
enum PaymentMethod { CASH, CARD }
enum UserRole { ADMIN, USER }

abstract class Person {
    public abstract String getUsername();
    public abstract String getAddress();
}

interface Displayable {
    String displayInfo();
}

class User extends Person {
    private final String username;
    private final String password;
    private final UserRole role;
    private String phoneNumber;
    private String address;

    public User(String username, String password, UserRole role, String phoneNumber, String address) {
        this.username = username;
        this.password = password;
        this.role = role;
        this.phoneNumber = phoneNumber;
        this.address = address;
    }

    @Override
    public String getUsername() { return username; }
    public String getPassword() { return password; }
    public UserRole getRole() { return role; }
    public String getPhoneNumber() { return phoneNumber; }
    @Override
    public String getAddress() { return address; }

    @Override
    public String toString() {
        return username + "|" + password + "|" + role + "|" + phoneNumber + "|" + address;
    }

    public static User fromString(String str) {
        String[] parts = str.split("\\|");
        if (parts.length >= 5) {
            return new User(parts[0], parts[1], UserRole.valueOf(parts[2]), parts[3], parts[4]);
        }
        return null;
    }
}

class AuthenticationSystem {
    private static final String USER_FILE = "users.txt";
    private static final String ORDERS_FILE = "orders.txt";

    public static boolean register(String username, String password, UserRole role, String phoneNumber, String address) {
        try {
            if (userExists(username)) {
                return false;
            }

            User newUser = new User(username, password, role, phoneNumber, address);
            Files.write(Paths.get(USER_FILE), 
                    (newUser.toString() + "\n").getBytes(),
                    StandardOpenOption.CREATE, StandardOpenOption.APPEND);
            return true;
        } catch (IOException e) {
            e.printStackTrace();
            return false;
        }
    }

    public static User login(String username, String password) {
        try {
            if (!Files.exists(Paths.get(USER_FILE))) {
                return null;
            }

            ArrayList<String> users = new ArrayList<>(Files.readAllLines(Paths.get(USER_FILE)));
            for (String user : users) {
                User u = User.fromString(user);
                if (u != null && u.getUsername().equals(username) && u.getPassword().equals(password)) {
                    return u;
                }
            }
            return null;
        } catch (IOException e) {
            e.printStackTrace();
            return null;
        }
    }

    private static boolean userExists(String username) throws IOException {
        if (!Files.exists(Paths.get(USER_FILE))) {
            return false;
        }

        ArrayList<String> users = new ArrayList<>(Files.readAllLines(Paths.get(USER_FILE)));
        for (String user : users) {
            User u = User.fromString(user);
            if (u != null && u.getUsername().equals(username)) {
                return true;
            }
        }
        return false;
    }

    public static void saveOrder(Order order, String username) {
        try {
            String orderData = "=== Order by: " + username + " ===\n" + 
                            order.getReceipt() + "\n\n";
            Files.write(Paths.get(ORDERS_FILE), 
                    orderData.getBytes(),
                    StandardOpenOption.CREATE, StandardOpenOption.APPEND);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public static ArrayList<String> getAllOrders() {
        try {
            if (!Files.exists(Paths.get(ORDERS_FILE))) {
                return new ArrayList<>();
            }
            return new ArrayList<>(Files.readAllLines(Paths.get(ORDERS_FILE)));
        } catch (IOException e) {
            e.printStackTrace();
            return new ArrayList<>();
        }
    }

    public static ArrayList<User> getAllUsers() {
        ArrayList<User> users = new ArrayList<>();
        try {
            if (!Files.exists(Paths.get(USER_FILE))) {
                return users;
            }

            ArrayList<String> userLines = new ArrayList<>(Files.readAllLines(Paths.get(USER_FILE)));
            for (String line : userLines) {
                User u = User.fromString(line);
                if (u != null) {
                    users.add(u);
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return users;
    }
}

class FoodItem implements Displayable {
    private final String name;
    private final FoodType type;
    private final double cost;
    private final Set<AllergyTag> allergyTags;
    private final String description;

    public FoodItem(String name, FoodType type, double cost, Set<AllergyTag> allergyTags, String description) {
        this.name = name;
        this.type = type;
        this.cost = cost;
        this.allergyTags = allergyTags;
        this.description = description;
    }

    public String getName() { return name; }
    public FoodType getType() { return type; }
    public double getCost() { return cost; }
    public Set<AllergyTag> getAllergyTags() { return allergyTags; }
    public String getDescription() { return description; }

    @Override
    public String toString() {
        return String.format("%-25s | RM%7.2f | %-30s | Diet Type: %s",
                name, cost, description, allergyTags.isEmpty() ? "-" : allergyTags);
    }

    @Override
    public String displayInfo() {
        return toString();
    }
}

class OrderItem {
    private final FoodItem foodItem;
    private int quantity;
    private String specialRequest;

    public OrderItem(FoodItem foodItem, int quantity, String specialRequest) {
        this.foodItem = foodItem;
        this.quantity = quantity;
        this.specialRequest = specialRequest;
    }

    public double getTotal() { return foodItem.getCost() * quantity; }
    public String getSpecialRequest() { return specialRequest.isEmpty() ? "-" : specialRequest; }
    public FoodItem getFoodItem() { return foodItem; }
    public int getQuantity() { return quantity; }
    public void setQuantity(int quantity) { this.quantity = quantity; }
}

class Order {
    private static int nextId = 1;
    private final int id;
    private final ArrayList<OrderItem> items;
    private final LocalDateTime orderTime;
    private static final DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm");
    private OrderStatus status;
    private PaymentMethod paymentMethod;
    private double cashAmount;
    private String cardNumber;

    public Order() {
        this.id = nextId++;
        this.items = new ArrayList<>();
        this.orderTime = LocalDateTime.now();
        this.status = OrderStatus.PENDING;
    }

    public void addItem(OrderItem item) { items.add(item); }
    public double getTotal() { return items.stream().mapToDouble(OrderItem::getTotal).sum(); }
    public void completePayment(PaymentMethod method, double cashAmount, String cardNumber) {
        this.paymentMethod = method;
        this.status = OrderStatus.PURCHASED;
        this.cashAmount = cashAmount;
        this.cardNumber = cardNumber;
    }
    public boolean isEmpty() { return items.isEmpty(); }
    public ArrayList<OrderItem> getItems() { return items; }
    public OrderStatus getStatus() { return status; }
    public int getId() { return id; }

    public String getReceipt() {
        StringBuilder sb = new StringBuilder();
        sb.append("\n=== ORDER RECEIPT ===\n");
        sb.append("Order Time: ").append(orderTime.format(formatter)).append("\n\n");
        
        sb.append(String.format("%-4s %-20s %-10s %-15s %-10s %-8s %-12s %-25s %-15s\n", 
            "No", "Name", "Type", "Special Req", "Price", "Qty", "Subtotal", "Description", "Diet Type"));
        sb.append("============================================================================================================================\n");
        
        for (int i = 0; i < items.size(); i++) {
            OrderItem item = items.get(i);
            FoodItem food = item.getFoodItem();
            
            String formattedQty = String.format("%,d", item.getQuantity());
            String formattedSubtotal = String.format("%,.2f", item.getTotal());
            
            sb.append(String.format("%-4d %-20s %-10s %-15s %-10s %-8s %-12s %-25s %-15s\n",
                i + 1,
                truncate(food.getName(), 20),
                truncate(food.getType().toString().toLowerCase(), 10),
                truncate(item.getSpecialRequest(), 15),
                "RM" + String.format("%.2f", food.getCost()),
                formattedQty,
                "RM" + formattedSubtotal,
                truncate(food.getDescription(), 35),
                formatAllergyTags(food.getAllergyTags())
            ));
        }
        sb.append("============================================================================================================================\n");

        sb.append(String.format("\n%-25s: RM%.2f", "TOTAL", getTotal()));
        if (status == OrderStatus.PURCHASED) {
            if (paymentMethod == PaymentMethod.CASH) {
                sb.append(String.format("\n%-25s: RM%.2f", "CASH", cashAmount));
                sb.append(String.format("\n%-25s: RM%.2f", "CHANGE", cashAmount - getTotal()));
                sb.append("\nPaid by cash");
            } else if (paymentMethod == PaymentMethod.CARD) {
                sb.append("\nPaid by card");
            }
        } else if (paymentMethod == PaymentMethod.CASH) {
            sb.append(String.format("\n%-25s: RM%.2f", "CASH", cashAmount));
            sb.append(String.format("\n%-25s: RM%.2f", "CHANGE", cashAmount - getTotal()));
        }
        
        if (status == OrderStatus.PENDING) {
            sb.append("\n\nORDER STATUS: PENDING");
        } else if (status == OrderStatus.PURCHASED) {
            sb.append("\n\nORDER STATUS: PAID");
        }
        return sb.toString();
    }

    private String formatAllergyTags(Set<AllergyTag> tags) {
        if (tags.isEmpty()) return "-";
        return tags.toString().replaceAll("[\\[\\]]", "");
    }

    private String truncate(String text, int maxLength) {
        if (text == null) return "";
        return text.length() > maxLength ? text.substring(0, maxLength-3) + "..." : text;
    }
}

class EmptyCartException extends Exception {
    public EmptyCartException(String message) {
        super(message);
    }
}

public class BakeryOrderingSystem {
    private final ArrayList<FoodItem> menuArrayList;
    private Order currentOrder;
    private User currentUser;

    public BakeryOrderingSystem() {
        this.menuArrayList = initializeMenuArrayList();
    }

    private ArrayList<FoodItem> initializeMenuArrayList() {
        ArrayList<FoodItem> menu = new ArrayList<>();
        
        menu.add(new FoodItem("Gardenia Bread", FoodType.BREAD, 4.50, 
            EnumSet.of(AllergyTag.DAIRY_FREE), "Soft white bread"));
        menu.add(new FoodItem("Wholemeal Bread", FoodType.BREAD, 5.20, 
            EnumSet.of(AllergyTag.DAIRY_FREE, AllergyTag.GLUTEN_FREE), "Healthy whole grain bread"));
        menu.add(new FoodItem("Baguette", FoodType.BREAD, 6.80, 
            EnumSet.of(AllergyTag.VEGAN), "Traditional French bread"));
        
        menu.add(new FoodItem("Chocolate Cake", FoodType.CAKE, 45.00, 
            EnumSet.noneOf(AllergyTag.class), "Rich chocolate flavor"));
        menu.add(new FoodItem("Red Velvet Cake", FoodType.CAKE, 55.00, 
            EnumSet.of(AllergyTag.NUT_FREE), "Classic red velvet cake with cream cheese frosting"));
        menu.add(new FoodItem("Cheesecake", FoodType.CAKE, 38.00, 
            EnumSet.of(AllergyTag.GLUTEN_FREE), "Creamy New York style"));
        
        menu.add(new FoodItem("Butter Cookies", FoodType.BISCUIT, 12.50, 
            EnumSet.noneOf(AllergyTag.class), "Buttery and crumbly"));
        menu.add(new FoodItem("Almond Biscotti", FoodType.BISCUIT, 15.00, 
            EnumSet.of(AllergyTag.DAIRY_FREE), "Italian almond biscuits"));
        menu.add(new FoodItem("Shortbread", FoodType.BISCUIT, 10.80, 
            EnumSet.of(AllergyTag.NUT_FREE), "Scottish classic"));
        
        menu.add(new FoodItem("Croissant", FoodType.PASTRY, 6.80, 
            EnumSet.noneOf(AllergyTag.class), "Flaky French pastry"));
        menu.add(new FoodItem("Danish Pastry", FoodType.PASTRY, 7.50, 
            EnumSet.of(AllergyTag.NUT_FREE), "Sweet fruit-filled pastry"));
        menu.add(new FoodItem("Cinnamon Roll", FoodType.PASTRY, 8.20, 
            EnumSet.of(AllergyTag.VEGAN), "Gooey cinnamon goodness"));
        
        menu.add(new FoodItem("Iced Latte", FoodType.BEVERAGE, 8.50, 
            EnumSet.of(AllergyTag.VEGAN), "Cold coffee with milk"));
        menu.add(new FoodItem("Green Tea", FoodType.BEVERAGE, 5.00, 
            EnumSet.of(AllergyTag.VEGAN, AllergyTag.GLUTEN_FREE), "Refreshing Japanese green tea"));
        menu.add(new FoodItem("Hot Chocolate", FoodType.BEVERAGE, 7.20, 
            EnumSet.of(AllergyTag.NUT_FREE), "Creamy chocolate drink"));
        
        return menu;
    }

    private void showAuthMenu() {
        while (true) {
            String[] options = {"Login", "Register", "Exit"};
            int choice = JOptionPane.showOptionDialog(
                null,
                "=== BAKERY ORDERING SYSTEM ===\nPlease authenticate to continue",
                "Authentication",
                JOptionPane.DEFAULT_OPTION,
                JOptionPane.PLAIN_MESSAGE,
                null,
                options,
                options[0]
            );

            switch (choice) {
                case 0: // Login
                    if (login()) {
                        if (currentUser.getRole() == UserRole.ADMIN) {
                            displayAdminMenu();
                        } else {
                            displayMainMenu();
                        }
                        return;
                    }
                    break;
                case 1: // Register
                    register();
                    break;
                case 2: // Exit
                    JOptionPane.showMessageDialog(null, "Thank you for visiting our system! Hope you have a nice day!");
                    System.exit(0);
                    break;
                default:
                    if (confirmExit()) {
                        System.exit(0);
                    }
            }
        }
    }

    private boolean login() {
        JPanel panel = new JPanel(new GridLayout(3, 2));
        JTextField usernameField = new JTextField();
        JPasswordField passwordField = new JPasswordField();

        panel.add(new JLabel("Username:"));
        panel.add(usernameField);
        panel.add(new JLabel("Password:"));
        panel.add(passwordField);

        int result = JOptionPane.showConfirmDialog(
            null,
            panel,
            "Login",
            JOptionPane.OK_CANCEL_OPTION,
            JOptionPane.PLAIN_MESSAGE
        );

        if (result == JOptionPane.OK_OPTION) {
            String username = usernameField.getText().trim();
            String password = new String(passwordField.getPassword()).trim();

            if (username.isEmpty() || password.isEmpty()) {
                JOptionPane.showMessageDialog(null, "Username and password cannot be empty!");
                return false;
            }

            User user = AuthenticationSystem.login(username, password);
            if (user != null) {
                currentUser = user;
                JOptionPane.showMessageDialog(null, "Login successful! Welcome, " + username + "!");
                return true;
            } else {
                JOptionPane.showMessageDialog(null, "Invalid username or password!");
            }
        }
        return false;
    }

    private void register() {
        JPanel panel = new JPanel(new GridLayout(5, 2));
        JTextField usernameField = new JTextField();
        JPasswordField passwordField = new JPasswordField();
        JTextField phoneField = new JTextField();
        JTextField addressField = new JTextField();

        panel.add(new JLabel("Username:"));
        panel.add(usernameField);
        panel.add(new JLabel("Password:"));
        panel.add(passwordField);
        panel.add(new JLabel("Phone Number:"));
        panel.add(phoneField);
        panel.add(new JLabel("Address:"));
        panel.add(addressField);

        int result = JOptionPane.showConfirmDialog(
            null,
            panel,
            "Register",
            JOptionPane.OK_CANCEL_OPTION,
            JOptionPane.PLAIN_MESSAGE
        );

        if (result == JOptionPane.OK_OPTION) {
            String username = usernameField.getText().trim();
            String password = new String(passwordField.getPassword()).trim();
            String phone = phoneField.getText().trim();
            String address = addressField.getText().trim();

            try {
                if (username.isEmpty() || password.isEmpty() || phone.isEmpty() || address.isEmpty()) {
                    throw new IllegalArgumentException("Username, password, phone number, and address cannot be empty!");
                }

                if (AuthenticationSystem.register(username, password, UserRole.USER, phone, address)) {
                    JOptionPane.showMessageDialog(null, "Registration successful! You can now login.");
                } else {
                    JOptionPane.showMessageDialog(null, "Username already exists!");
                }
            } catch (IllegalArgumentException e) {
                JOptionPane.showMessageDialog(null, e.getMessage());
            }
        }
    }
    

    private void displayAdminMenu() {
        while (true) {
            String[] options = {
                "View All Orders",
                "View All Users",
                "Logout"
            };
            
            int choice = JOptionPane.showOptionDialog(
                null,
                "=== ADMIN DASHBOARD ===\nWelcome, " + currentUser.getUsername(),
                "Admin Menu",
                JOptionPane.DEFAULT_OPTION,
                JOptionPane.PLAIN_MESSAGE,
                null,
                options,
                options[0]
            );
            
            switch (choice) {
                case 0: viewAllOrders(); break;
                case 1: viewAllUsers(); break;
                case 2: 
                    if (confirmLogout()) {
                        currentUser = null;
                        showAuthMenu();
                        return;
                    }
                    break;
                default:
                    if (confirmExit()) {
                        System.exit(0);
                    }
            }
        }
    }

    private void viewAllOrders() {
        try {
            if (!Files.exists(Paths.get("orders.txt"))) {
                JOptionPane.showMessageDialog(null, "No orders found!");
                return;
            }

            String allOrders = new String(Files.readAllBytes(Paths.get("orders.txt")));
            JTextArea textArea = new JTextArea(allOrders);
            textArea.setFont(new Font("Monospaced", Font.PLAIN, 12));
            textArea.setEditable(false);
            JScrollPane scrollPane = new JScrollPane(textArea);
            scrollPane.setPreferredSize(new Dimension(800, 600));
            JOptionPane.showMessageDialog(null, scrollPane, "All Orders", JOptionPane.PLAIN_MESSAGE);
        } catch (IOException e) {
            e.printStackTrace();
            JOptionPane.showMessageDialog(null, "Error reading orders file!");
        }
    }

    private void viewAllUsers() {
        ArrayList<User> users = AuthenticationSystem.getAllUsers();
        if (users.isEmpty()) {
            JOptionPane.showMessageDialog(null, "No users found!");
            return;
        }

        StringBuilder sb = new StringBuilder();
        sb.append("=== ALL USERS ===\n\n");
        sb.append(String.format("%-20s %-10s %-15s %-30s\n", 
            "Username", "Role", "Phone", "Address"));
        sb.append("----------------------------------------------------------------\n");
        
        for (User user : users) {
            sb.append(String.format("%-20s %-10s %-15s %-30s\n",
                user.getUsername(),
                user.getRole(),
                user.getPhoneNumber(),
                truncate(user.getAddress(), 30)));
        }

        JTextArea textArea = new JTextArea(sb.toString());
        textArea.setFont(new Font("Monospaced", Font.PLAIN, 12));
        textArea.setEditable(false);
        JScrollPane scrollPane = new JScrollPane(textArea);
        scrollPane.setPreferredSize(new Dimension(800, 400));
        JOptionPane.showMessageDialog(null, scrollPane, "All Users", JOptionPane.PLAIN_MESSAGE);
    }

    private String truncate(String text, int maxLength) {
        if (text == null) return "";
        return text.length() > maxLength ? text.substring(0, maxLength-3) + "..." : text;
    }

    private void displayMainMenu() {
        while (true) {
            String[] options = {
                "Choose Food Type",
                "View Cart",
                "Proceed to Payment",
                "Logout"
            };
            
            int choice = JOptionPane.showOptionDialog(
                null,
                "---------Welcome to Our Bakery Ordering System-----------\n" +
                "This system mainly sells baked goods. Feel free to browse our menu.\n" +
                "---------------------------------------------------------",
                "Bakery Ordering System",
                JOptionPane.DEFAULT_OPTION,
                JOptionPane.PLAIN_MESSAGE,
                null,
                options,
                options[0]
            );
            
            switch (choice) {
                case 0: browseByFoodType(); break;
                case 1: viewCart(); break;
                case 2: processPayment(); break;
                case 3: 
                    if (confirmLogout()) {
                        currentUser = null;
                        showAuthMenu();
                        return;
                    }
                    break;
                default:
                    if (confirmExit()) {
                        System.exit(0);
                    }
            }
        }
    }

    private boolean confirmLogout() {
        if (currentOrder != null && !currentOrder.isEmpty() && currentOrder.getStatus() != OrderStatus.PURCHASED) {
            int result = JOptionPane.showConfirmDialog(
                null,
                "You have unpaid items in your cart!\n" + currentOrder.getReceipt() + 
                "\nAre you sure you want to logout?",
                "Confirm Logout",
                JOptionPane.YES_NO_OPTION
            );
            if (result == JOptionPane.YES_OPTION) {
                JOptionPane.showMessageDialog(null, "Thank you for using our Bakery Ordering System.");
                return true;
            } else {
                return false;
            }
        } else {
            int result = JOptionPane.showConfirmDialog(
                null,
                "Are you sure you want to logout?",
                "Confirm Logout",
                JOptionPane.YES_NO_OPTION
            );
            if (result == JOptionPane.YES_OPTION) {
                JOptionPane.showMessageDialog(null, "Thank you for using our Bakery Ordering System.");
                return true;
            } else {
                return false;
            }
        }
    }

    private boolean confirmExit() {
        if (currentOrder != null && !currentOrder.isEmpty() && currentOrder.getStatus() != OrderStatus.PURCHASED) {
            int result = JOptionPane.showConfirmDialog(
                null,
                "You have unpaid items in your cart!\n" + currentOrder.getReceipt() + 
                "\nAre you sure you want to exit?",
                "Confirm Exit",
                JOptionPane.YES_NO_OPTION
            );
            if (result == JOptionPane.YES_OPTION) {
                JOptionPane.showMessageDialog(null, "Thank you for visiting our system! Hope you have a nice day!");
            }
            return result == JOptionPane.YES_OPTION;
        }
        JOptionPane.showMessageDialog(null, "Thank you for visiting our system! Hope you have a nice day!");
        return true;
    }

    private void browseByFoodType() {
        while (true) {
            Object[] options = new Object[FoodType.values().length + 1];
            int i = 0;
            for (FoodType type : FoodType.values()) {
                options[i++] = type.toString().charAt(0) + type.toString().substring(1).toLowerCase();
            }
            options[i] = "Back to Main Menu";
            
            int choice = JOptionPane.showOptionDialog(
                null,
                "=== FOOD TYPES ===",
                "Select Food Type",
                JOptionPane.DEFAULT_OPTION,
                JOptionPane.PLAIN_MESSAGE,
                null,
                options,
                options[0]
            );
            
            if (choice == FoodType.values().length || choice == -1) {
                return;
            } else if (choice >= 0 && choice < FoodType.values().length) {
                FoodType selectedType = FoodType.values()[choice];
                browseFoodItems(selectedType);
            }
        }
    }

    private void browseFoodItems(FoodType type) {
        ArrayList<FoodItem> itemsArrayList = new ArrayList<>();
        for (FoodItem item : menuArrayList) {
            if (item.getType() == type) {
                itemsArrayList.add(item);
            }
        }
        
        while (true) {
            StringBuilder sb = new StringBuilder();
            sb.append("=== ").append(type.toString().toUpperCase()).append(" MENU ===\n\n");
            sb.append(String.format("%-4s %-25s | %-10s | %-40s | %-15s\n", "No.", "Name", "Price", "Description", "Diet Type"));
            sb.append("--------------------------------------------------------------------------------------------------\n");
            for (int i = 0; i < itemsArrayList.size(); i++) {
                FoodItem item = itemsArrayList.get(i);
                String formattedAllergies = item.getAllergyTags().isEmpty() ? "-" : item.getAllergyTags().toString();
                sb.append(String.format("%-4d %-25s | RM%7.2f | %-40s | %s\n",
                        i + 1,
                        truncate(item.getName(), 25),
                        item.getCost(),
                        truncate(item.getDescription(), 40),
                        formattedAllergies));
            }
            sb.append("--------------------------------------------------------------------------------------------------\n");

            JTextArea textArea = new JTextArea(sb.toString());
            textArea.setFont(new Font("Monospaced", Font.PLAIN, 12));
            textArea.setEditable(false);
            JScrollPane scrollPane = new JScrollPane(textArea);
            scrollPane.setPreferredSize(new Dimension(800, 400));
            
            String[] options = new String[itemsArrayList.size() + 1];
            for (int i = 0; i < itemsArrayList.size(); i++) {
                options[i] = String.valueOf(i + 1);
            }
            options[itemsArrayList.size()] = "Back";
            
            int choice = JOptionPane.showOptionDialog(
                null,
                scrollPane,
                type.toString() + " Menu",
                JOptionPane.DEFAULT_OPTION,
                JOptionPane.PLAIN_MESSAGE,
                null,
                options,
                options[0]
            );
            
            if (choice == itemsArrayList.size() || choice == -1) {
                return;
            } else if (choice >= 0 && choice < itemsArrayList.size()) {
                addToCart(itemsArrayList.get(choice));
            }
        }
    }

    private void addToCart(FoodItem item) {
        try {
            String quantityStr = JOptionPane.showInputDialog(
                null,
                "Enter quantity for " + item.getName() + " (0 to cancel):",
                "Add to Cart",
                JOptionPane.QUESTION_MESSAGE
            );
            
            if (quantityStr == null) return;
            
            int quantity = Integer.parseInt(quantityStr);
            
            if (quantity < 0) {
                JOptionPane.showMessageDialog(null, "Quantity cannot be negative! Item not added.");
                return;
            } else if (quantity == 0) {
                JOptionPane.showMessageDialog(null, "Item not added to cart.");
                return;
            }

            if (currentOrder != null) {
                for (OrderItem orderItem : currentOrder.getItems()) {
                    if (orderItem.getFoodItem().getName().equals(item.getName()) && 
                        orderItem.getSpecialRequest().equals("")) {
                        int updateChoice = JOptionPane.showConfirmDialog(
                            null,
                            "This item is already in your cart. Update quantity instead?",
                            "Update Quantity",
                            JOptionPane.YES_NO_OPTION
                        );
                        if (updateChoice == JOptionPane.YES_OPTION) {
                            orderItem.setQuantity(orderItem.getQuantity() + quantity);
                            JOptionPane.showMessageDialog(
                                null,
                                "Quantity updated! Total now: " + orderItem.getQuantity()
                            );
                            return;
                        }
                    }
                }
            }

            String specialRequest = "";
            if (item.getType() == FoodType.CAKE) {
                Object[] options = {"1 Piece", "500g", "1kg", "1.5kg", "2kg"};
                int choice = JOptionPane.showOptionDialog(
                    null,
                    "Select cake size:",
                    "Cake Size",
                    JOptionPane.DEFAULT_OPTION,
                    JOptionPane.QUESTION_MESSAGE,
                    null,
                    options,
                    options[0]
                );
                
                if (choice == -1) return; // User cancelled
                specialRequest = options[choice].toString();
            } 
            else if (item.getType() == FoodType.BEVERAGE) {
                Object[] options = {"Regular", "Less Ice", "Less Sugar", "Less Ice & Sugar", "No Ice", "No Sugar"};
                int choice = JOptionPane.showOptionDialog(
                    null,
                    "Select beverage preference:",
                    "Beverage Options",
                    JOptionPane.DEFAULT_OPTION,
                    JOptionPane.QUESTION_MESSAGE,
                    null,
                    options,
                    options[0]
                );
                
                if (choice == -1) return; // User cancelled
                specialRequest = options[choice].toString();
            }
            else if (item.getType() == FoodType.BREAD) {
                Object[] options = {"Sliced", "Unsliced", "Half-sliced"};
                int choice = JOptionPane.showOptionDialog(
                    null,
                    "Select bread preparation:",
                    "Bread Options",
                    JOptionPane.DEFAULT_OPTION,
                    JOptionPane.QUESTION_MESSAGE,
                    null,
                    options,
                    options[0]
                );
                
                if (choice == -1) return; // User cancelled
                specialRequest = options[choice].toString();
            }

            if (currentOrder == null) {
                currentOrder = new Order();
            }
            currentOrder.addItem(new OrderItem(item, quantity, specialRequest));
            JOptionPane.showMessageDialog(
                null,
                quantity + " x " + item.getName() + " added to cart!"
            );
        } catch (NumberFormatException e) {
            JOptionPane.showMessageDialog(null, "Invalid input! Please enter a valid number.");
        }
    }

    private void viewCart() {
        while (true) {
            if (currentOrder == null || currentOrder.isEmpty()) {
                JOptionPane.showMessageDialog(null, "Your cart is empty!");
                return;
            }

            JTextArea textArea = new JTextArea(currentOrder.getReceipt());
            textArea.setFont(new Font("Monospaced", Font.PLAIN, 12));
            textArea.setEditable(false);
            
            JScrollPane scrollPane = new JScrollPane(textArea);
            scrollPane.setPreferredSize(new java.awt.Dimension(800, 400));
            
            Object[] options = {"Remove Item", "Back to Main Menu"};
            int choice = JOptionPane.showOptionDialog(
                null,
                scrollPane,
                "Your Cart",
                JOptionPane.DEFAULT_OPTION,
                JOptionPane.PLAIN_MESSAGE,
                null,
                options,
                options[0]
            );
            
            if (choice == 0) {
                String itemNumStr = JOptionPane.showInputDialog(
                    null,
                    "Enter item number to remove:",
                    "Remove Item",
                    JOptionPane.QUESTION_MESSAGE
                );
                
                if (itemNumStr != null) {
                    try {
                        int itemNum = Integer.parseInt(itemNumStr);
                        if (itemNum > 0 && itemNum <= currentOrder.getItems().size()) {
                            OrderItem removed = currentOrder.getItems().remove(itemNum - 1);
                            JOptionPane.showMessageDialog(
                                null,
                                "Removed: " + removed.getQuantity() + " x " + removed.getFoodItem().getName()
                            );
                            
                            if (currentOrder.isEmpty()) {
                                currentOrder = null;
                                JOptionPane.showMessageDialog(null, "Your cart is now empty!");
                                return;
                            }
                            // Continue loop to show updated cart
                        } else {
                            JOptionPane.showMessageDialog(null, "Invalid item number!");
                        }
                    } catch (NumberFormatException e) {
                        JOptionPane.showMessageDialog(null, "Invalid input! Please enter a number.");
                    }
                }
            } else {
                // Back to Main Menu
                return;
            }
        }
    }

    private void processPayment() {
        try {
            if (currentOrder == null || currentOrder.isEmpty()) {
                throw new EmptyCartException("Your cart is empty");
            }

            Object[] paymentOptions = { "Cash", "Card" };
            int paymentChoice = JOptionPane.showOptionDialog(null,
                    "Select a payment method:",
                    "Payment",
                    JOptionPane.DEFAULT_OPTION,
                    JOptionPane.QUESTION_MESSAGE,
                    null,
                    paymentOptions,
                    paymentOptions[0]);

            if (paymentChoice == -1) return;

            PaymentMethod method = (paymentChoice == 0) ? PaymentMethod.CASH : PaymentMethod.CARD;
            double total = currentOrder.getTotal();
            double cashAmount = 0;
            String cardNumber = "";

            if (method == PaymentMethod.CASH) {
                while (true) {
                    try {
                        String cashInput = JOptionPane.showInputDialog(null, String.format("Total amount: RM%.2f\nEnter cash amount:", total), "Cash Payment", JOptionPane.PLAIN_MESSAGE);
                        if (cashInput == null) return;
                        cashAmount = Double.parseDouble(cashInput);
                        if (cashAmount >= total) break;
                        JOptionPane.showMessageDialog(null, "Insufficient cash. Please enter a valid amount.", "Error", JOptionPane.ERROR_MESSAGE);
                    } catch (NumberFormatException e) {
                        JOptionPane.showMessageDialog(null, "Invalid amount. Please enter a number.", "Error", JOptionPane.ERROR_MESSAGE);
                    }
                }
            } else {
                while (true) {
                    cardNumber = JOptionPane.showInputDialog(null, "Enter card number (format: xxxx-xxxx-xxxx-xxxx):", "Card Payment", JOptionPane.PLAIN_MESSAGE);
                    if (cardNumber == null) return;
                    if (cardNumber.matches("\\d{4}-\\d{4}-\\d{4}-\\d{4}")) break;
                    JOptionPane.showMessageDialog(null, "Invalid card number. Please enter in the format xxxx-xxxx-xxxx-xxxx.", "Error", JOptionPane.ERROR_MESSAGE);
                }
            }

            currentOrder.completePayment(method, cashAmount, cardNumber);
            AuthenticationSystem.saveOrder(currentOrder, currentUser.getUsername());
            
            String receipt = currentOrder.getReceipt();

            JTextArea textArea = new JTextArea(receipt);
            textArea.setFont(new Font("Monospaced", Font.PLAIN, 12));
            textArea.setEditable(false);
            
            JScrollPane scrollPane = new JScrollPane(textArea);
            scrollPane.setPreferredSize(new Dimension(800, 600));

            JOptionPane.showMessageDialog(null, scrollPane, "Receipt", JOptionPane.INFORMATION_MESSAGE);

            currentOrder = new Order();
        } catch (EmptyCartException e) {
            JOptionPane.showMessageDialog(null, e.getMessage(), "Payment", JOptionPane.WARNING_MESSAGE);
            // Just return to main menu
        }
    }

    public static void main(String[] args) {
        // Create default admin account if not exists
        try {
            if (!Files.exists(Paths.get("users.txt")) || 
                AuthenticationSystem.getAllUsers().stream().noneMatch(u -> u.getRole() == UserRole.ADMIN)) {
                AuthenticationSystem.register("admin", "admin123", UserRole.ADMIN, "0123456789", "Admin Address");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        SwingUtilities.invokeLater(() -> {
            BakeryOrderingSystem system = new BakeryOrderingSystem();
            system.showAuthMenu();
        });
    }
}