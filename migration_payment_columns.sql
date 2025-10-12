-- Migration script to add payment method amount columns to payment_receipts table
-- Run this script to update the database schema for the new payment system

USE elitelink_new;

-- Add the new amount columns for each payment method
ALTER TABLE payment_receipts 
ADD COLUMN cash_amount DECIMAL(10,2) DEFAULT 0.00 AFTER payment_amount,
ADD COLUMN cheque_amount DECIMAL(10,2) DEFAULT 0.00 AFTER cash_amount,
ADD COLUMN bank_transfer_amount DECIMAL(10,2) DEFAULT 0.00 AFTER cheque_amount;

-- Update the payment_type enum to include 'multiple' option
ALTER TABLE payment_receipts 
MODIFY COLUMN payment_type ENUM('cash', 'bank_transfer', 'cheque', 'multiple') DEFAULT 'cash';

-- Optional: Add a constraint to ensure breakdown amounts don't exceed total
-- ALTER TABLE payment_receipts 
-- ADD CONSTRAINT chk_payment_breakdown 
-- CHECK (cash_amount + cheque_amount + bank_transfer_amount <= payment_amount);

SELECT 'Migration completed successfully!' as status;