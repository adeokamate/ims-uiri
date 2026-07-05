<?php
/**
 * UIRI IMS - API & Function Documentation
 * Complete reference for all available functions
 */

// ============================================================
// AUTHENTICATION & SESSION FUNCTIONS
// ============================================================

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn(): bool {}

/**
 * Require user to be logged in
 * Redirects to login if not authenticated
 * @return void
 */
function requireLogin(): void {}

/**
 * Get currently logged-in user data
 * @return array User array with id, name, email, role, branch_id, etc.
 */
function currentUser(): array {}

/**
 * Check if user has specific role(s)
 * @param string ...$roles Role names to check
 * @return bool
 */
function hasRole(string ...$roles): bool {}

/**
 * Require specific role(s)
 * Redirects to error page if user doesn't have role
 * @param string ...$roles Role names to require
 * @return void
 */
function requireRole(string ...$roles): void {}

// ============================================================
// ROLE-BASED ACCESS CONTROL
// ============================================================

/**
 * Check if user has role or higher in hierarchy
 * Hierarchy: Administrator > Campus Manager > Store Manager > Section Manager > Staff
 * @param string $requiredRole Role to check
 * @return bool
 */
function hasRoleOrAbove(string $requiredRole): bool {}

/**
 * Check if user can access a specific branch
 * Admin can access all, others only their assigned branch
 * @param int $branchId Branch ID to verify
 * @return bool
 */
function canAccessBranch(int $branchId): bool {}

/**
 * Check if user can access a specific inventory item
 * Checks based on item's branch
 * @param int $itemId Item ID to verify
 * @return bool
 */
function canAccessItem(int $itemId): bool {}

// ============================================================
// SECURITY FUNCTIONS
// ============================================================

/**
 * Generate CSRF token
 * @return string 64-character hex token
 */
function csrfToken(): string {}

/**
 * Verify CSRF token from POST
 * Dies with 403 if invalid
 * @return void
 */
function verifyCsrf(): void {}

/**
 * Validate password strength
 * Requirements: 8+ chars, uppercase, lowercase, number
 * @param string $password Password to validate
 * @param string &$error Error message (passed by reference)
 * @return bool
 */
function validatePassword(string $password, string &$error = ''): bool {}

/**
 * Sanitize and escape string for output
 * Uses htmlspecialchars with UTF-8
 * @param string $val Value to clean
 * @return string
 */
function clean(string $val): string {}

// ============================================================
// DATABASE FUNCTIONS
// ============================================================

/**
 * Get PDO database connection
 * Singleton pattern - returns same connection
 * @return PDO
 */
function db(): PDO {}

/**
 * Log action to audit trail
 * @param string $action Action type (LOGIN, LOGOUT, CREATE, UPDATE, DELETE, etc.)
 * @param string $table Table name affected
 * @param int $recordId Record ID affected
 * @param string $details Additional details
 * @return void
 */
function auditLog(string $action, string $table = '', int $recordId = 0, string $details = ''): void {}

// ============================================================
// FLASH MESSAGES (Session-based notifications)
// ============================================================

/**
 * Set flash message for next page load
 * @param string $type Type: 'success', 'error', 'warning', 'info'
 * @param string $message Message text
 * @return void
 */
function setFlash(string $type, string $message): void {}

/**
 * Get and clear flash message
 * Returns null if no flash set
 * @return array|null ['type' => string, 'message' => string]
 */
function getFlash(): ?array {}

// ============================================================
// NOTIFICATION FUNCTIONS
// ============================================================

/**
 * Send notification to user or broadcast to branch
 * @param int|null $userId User ID (null to broadcast)
 * @param int|null $branchId Branch ID (for broadcasts)
 * @param string $type Notification type
 * @param string $title Title text
 * @param string $message Message content (optional)
 * @param string $link Link to related page (optional)
 * @return void
 */
function notify(?int $userId, ?int $branchId, string $type, string $title, string $message = '', string $link = ''): void {}

/**
 * Get unread notifications for user
 * @param array $user User array from session
 * @param int $limit Max notifications to return
 * @return array
 */
function unreadNotifications(array $user, int $limit = 8): array {}

/**
 * Get count of unread notifications
 * @param array $user User array from session
 * @return int
 */
function unreadNotificationCount(array $user): int {}

/**
 * Send low stock alert to branch if item below minimum
 * Prevents duplicate notifications while one is unread
 * @param array $item Item data array
 * @return void
 */
function maybeNotifyLowStock(array $item): void {}

// ============================================================
// CURRENCY FORMATTING
// ============================================================

/**
 * Format amount as Ugandan Shillings
 * @param float $amount Amount to format
 * @return string (e.g., "UGX 2,500,000")
 */
function ugx(float $amount): string {}

// ============================================================
// DATE FORMATTING FUNCTIONS
// ============================================================

/**
 * Format date to readable format
 * @param string $datetime DateTime string
 * @return string (e.g., "15 May 2026")
 */
function formatDate($datetime): string {}

/**
 * Format datetime to readable format
 * @param string $datetime DateTime string
 * @return string (e.g., "15 May 2026 14:30")
 */
function formatDateTime($datetime): string {}

/**
 * Format time since to human-readable
 * @param string $datetime DateTime string
 * @return string (e.g., "2 hours ago", "just now", "3 days ago")
 */
function formatTimeSince($datetime): string {}

// ============================================================
// INVENTORY ITEM FUNCTIONS
// ============================================================

/**
 * Generate unique item code
 * Format: BR-CAT-### (e.g., NK-ICT-001)
 * @param int $categoryId Category ID
 * @param int $branchId Branch ID
 * @return string
 */
function generateItemCode(int $categoryId, int $branchId): string {}

/**
 * Get item total value (quantity * unit_price)
 * @param array $item Item data array
 * @return float
 */
function getItemValue(array $item): float {}

/**
 * Check if item can be deleted
 * Returns false if item has stock transactions
 * @param int $itemId Item ID
 * @return bool
 */
function canDeleteItem(int $itemId): bool {}

/**
 * Check stock availability
 * @param int $itemId Item ID
 * @param int $branchId Branch ID
 * @param int $quantity Quantity needed
 * @return bool
 */
function checkStockAvailability(int $itemId, int $branchId, int $quantity): bool {}

/**
 * Get low stock items for branch
 * @param int $branchId Branch ID (0 = all)
 * @param int $limit Max items to return
 * @return array
 */
function getLowStockItems(int $branchId = 0, int $limit = 10): array {}

/**
 * Get total inventory value
 * @param int $branchId Branch ID (0 = all)
 * @return float
 */
function getTotalInventoryValue(int $branchId = 0): float {}

/**
 * Get inventory item count
 * @param int $branchId Branch ID (0 = all)
 * @return int
 */
function getInventoryCount(int $branchId = 0): int {}

/**
 * Get asset depreciation value (simplified 15% per year)
 * @param array $item Item data
 * @param int $yearsInUse Years owned
 * @return float
 */
function getDepreciationValue(array $item, int $yearsInUse = 0): float {}

/**
 * Get warranty status text
 * @param array $item Item data
 * @return string (e.g., "Active until 15 May 2027", "Expired", "No warranty")
 */
function getWarrantyStatus(array $item): string {}

// ============================================================
// STOCK TRANSACTION FUNCTIONS
// ============================================================

/**
 * Log stock transaction
 * @param int $itemId Item ID
 * @param int $branchId Branch ID
 * @param int $userId User ID
 * @param string $type Transaction type (stock_in, stock_out, transfer_in, transfer_out, adjustment)
 * @param int $quantity Quantity
 * @param float $unitPrice Unit price (optional)
 * @param string $reference Reference number (optional)
 * @param string $remarks Remarks (optional)
 * @return int Transaction ID
 */
function logStockTransaction(int $itemId, int $branchId, int $userId, string $type, int $quantity, float $unitPrice = 0, string $reference = '', string $remarks = ''): int {}

/**
 * Update item stock level
 * @param int $itemId Item ID
 * @param int $quantityChange Change amount (positive or negative)
 * @return void
 */
function updateItemStock(int $itemId, int $quantityChange): void {}

/**
 * Get monthly stock in value
 * @param int $branchId Branch ID (0 = all)
 * @param int $month Month (default: current)
 * @param int $year Year (default: current)
 * @return float
 */
function getMonthlyStockInValue(int $branchId = 0, int $month = 0, int $year = 0): float {}

/**
 * Get monthly stock out value
 * @param int $branchId Branch ID (0 = all)
 * @param int $month Month (default: current)
 * @param int $year Year (default: current)
 * @return float
 */
function getMonthlyStockOutValue(int $branchId = 0, int $month = 0, int $year = 0): float {}

// ============================================================
// REQUEST & TRANSFER FUNCTIONS
// ============================================================

/**
 * Generate unique request code
 * Format: REQ-YYYYMMDD-###
 * @return string
 */
function generateRequestCode(): string {}

/**
 * Generate unique transfer code
 * Format: TR-YYYYMMDD-###
 * @return string
 */
function generateTransferCode(): string {}

/**
 * Get pending requests count
 * @param int $branchId Branch ID (0 = all)
 * @return int
 */
function getPendingRequestsCount(int $branchId = 0): int {}

/**
 * Get active transfers count
 * @param int $branchId Branch ID (0 = all)
 * @return int
 */
function getActiveTransfersCount(int $branchId = 0): int {}

/**
 * Get request status badge CSS class
 * @param string $status Request status
 * @return string CSS class name
 */
function getRequestStatusClass(string $status): string {}

/**
 * Get transfer status badge CSS class
 * @param string $status Transfer status
 * @return string CSS class name
 */
function getTransferStatusClass(string $status): string {}

// ============================================================
// SUPPLIER FUNCTIONS
// ============================================================

/**
 * Check if supplier can be deleted
 * @param int $supplierId Supplier ID
 * @return bool
 */
function canDeleteSupplier(int $supplierId): bool {}

/**
 * Get top suppliers by purchase value
 * @param int $limit Max suppliers to return
 * @param int $branchId Branch ID (0 = all)
 * @return array
 */
function getTopSuppliers(int $limit = 5, int $branchId = 0): array {}

// ============================================================
// USER FUNCTIONS
// ============================================================

/**
 * Get user's last login time
 * @param int $userId User ID
 * @return string|null DateTime string or null
 */
function getLastLogin(int $userId): ?string {}

/**
 * Send notification to specific user
 * @param int $userId User ID
 * @param string $type Notification type
 * @param string $title Title
 * @param string $message Message (optional)
 * @param string $link Link (optional)
 * @return void
 */
function sendNotificationToUser(int $userId, string $type, string $title, string $message = '', string $link = ''): void {}

/**
 * Send notification to branch (broadcast)
 * @param int $branchId Branch ID
 * @param string $type Notification type
 * @param string $title Title
 * @param string $message Message (optional)
 * @param string $link Link (optional)
 * @return void
 */
function sendNotificationToBranch(int $branchId, string $type, string $title, string $message = '', string $link = ''): void {}

// ============================================================
// CONSTANTS
// ============================================================

// Database and Site Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'uiri_ims');
define('SITE_NAME', 'UIRI Inventory System');
define('SITE_SHORT', 'UIRI IMS');
define('BASE_URL', 'http://localhost/uiri-ims/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/items/');
define('UPLOAD_URL', BASE_URL . 'uploads/items/');

// Role Hierarchy
define('ROLE_ADMIN', 'Administrator');
define('ROLE_CAMPUS_MGR', 'Campus Manager');
define('ROLE_STORE_MGR', 'Store Manager');
define('ROLE_SECTION_MGR', 'Section Manager');
define('ROLE_STAFF', 'Staff');

// Transaction Types
define('TX_STOCK_IN', 'stock_in');
define('TX_STOCK_OUT', 'stock_out');
define('TX_TRANSFER_IN', 'transfer_in');
define('TX_TRANSFER_OUT', 'transfer_out');
define('TX_ADJUSTMENT', 'adjustment');

// Request Statuses
define('REQ_PENDING', 'Pending');
define('REQ_APPROVED', 'Approved');
define('REQ_REJECTED', 'Rejected');
define('REQ_ISSUED', 'Issued');
define('REQ_CANCELLED', 'Cancelled');

// Transfer Statuses
define('TRANSFER_REQUESTED', 'Requested');
define('TRANSFER_APPROVED', 'Approved');
define('TRANSFER_DISPATCHED', 'Dispatched');
define('TRANSFER_RECEIVED', 'Received');
define('TRANSFER_REJECTED', 'Rejected');
define('TRANSFER_CANCELLED', 'Cancelled');

// Notification Types
define('NOTIF_LOW_STOCK', 'low_stock');
define('NOTIF_PENDING_REQUESTS', 'pending_requests');
define('NOTIF_TRANSFERS', 'transfers');
define('NOTIF_APPROVALS', 'approvals');

// Audit Actions
define('AUDIT_LOGIN', 'LOGIN');
define('AUDIT_LOGOUT', 'LOGOUT');
define('AUDIT_CREATE', 'CREATE');
define('AUDIT_UPDATE', 'UPDATE');
define('AUDIT_DELETE', 'DELETE');
define('AUDIT_APPROVE', 'APPROVE');
define('AUDIT_REJECT', 'REJECT');
define('AUDIT_ISSUE', 'ISSUE');
define('AUDIT_TRANSFER', 'TRANSFER');
define('AUDIT_ADJUST_STOCK', 'ADJUST_STOCK');

?>
