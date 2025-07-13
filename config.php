<?php
// Start session
session_start();

// Initialize customers array in session if not exists
if (!isset($_SESSION['customers'])) {
    $_SESSION['customers'] = [];
}

// Include Customer class
require_once 'Customer.php';

// Function to add customer to session
function addCustomer($customer) {
    $_SESSION['customers'][] = $customer->toArray();
}

// Function to get all customers from session
function getAllCustomers() {
    $customers = [];
    if (isset($_SESSION['customers'])) {
        foreach ($_SESSION['customers'] as $customerData) {
            $customers[] = Customer::fromArray($customerData);
        }
    }
    return $customers;
}

// Function to check if username exists
function usernameExists($username) {
    if (isset($_SESSION['customers'])) {
        foreach ($_SESSION['customers'] as $customerData) {
            if ($customerData['username'] === $username) {
                return true;
            }
        }
    }
    return false;
}

// Function to generate next ID
function getNextId() {
    if (empty($_SESSION['customers'])) {
        return 1;
    }
    $maxId = 0;
    foreach ($_SESSION['customers'] as $customerData) {
        if ($customerData['id'] > $maxId) {
            $maxId = $customerData['id'];
        }
    }
    return $maxId + 1;
}

// Function to clear all customers (for testing)
function clearAllCustomers() {
    $_SESSION['customers'] = [];
}

// Function to delete customer by ID
function deleteCustomer($id) {
    if (isset($_SESSION['customers'])) {
        foreach ($_SESSION['customers'] as $key => $customerData) {
            if ($customerData['id'] == $id) {
                unset($_SESSION['customers'][$key]);
                $_SESSION['customers'] = array_values($_SESSION['customers']); // Re-index array
                return true;
            }
        }
    }
    return false;
}

// Function to get customer by ID
function getCustomerById($id) {
    if (isset($_SESSION['customers'])) {
        foreach ($_SESSION['customers'] as $customerData) {
            if ($customerData['id'] == $id) {
                return Customer::fromArray($customerData);
            }
        }
    }
    return null;
}

// Function to update customer
function updateCustomer($id, $updatedCustomer) {
    if (isset($_SESSION['customers'])) {
        foreach ($_SESSION['customers'] as $key => $customerData) {
            if ($customerData['id'] == $id) {
                $_SESSION['customers'][$key] = $updatedCustomer->toArray();
                return true;
            }
        }
    }
    return false;
}

// Function to search customers by keyword
function searchCustomers($keyword) {
    $results = [];
    if (isset($_SESSION['customers'])) {
        foreach ($_SESSION['customers'] as $customerData) {
            if (stripos($customerData['fullname'], $keyword) !== false ||
                stripos($customerData['username'], $keyword) !== false ||
                stripos($customerData['address'], $keyword) !== false ||
                stripos($customerData['phone'], $keyword) !== false) {
                $results[] = Customer::fromArray($customerData);
            }
        }
    }
    return $results;
}

// Function to get statistics
function getCustomerStats() {
    $customers = getAllCustomers();
    $stats = [
        'total' => count($customers),
        'male' => 0,
        'female' => 0,
        'other' => 0
    ];
    
    foreach ($customers as $customer) {
        switch (strtolower($customer->getGender())) {
            case 'nam':
                $stats['male']++;
                break;
            case 'nữ':
                $stats['female']++;
                break;
            default:
                $stats['other']++;
                break;
        }
    }
    
    return $stats;
}
?>
