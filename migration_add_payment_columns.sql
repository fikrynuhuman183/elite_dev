-- Migration to add payment method amount columns to payment_receipts table
-- Execute this SQL to update the database schema

-- Add the new amount columns for each payment method
ALTER TABLE payment_receipts 
ADD COLUMN cash_amount DECIMAL(10,2) DEFAULT 0.00 AFTER payment_amount,
ADD COLUMN cheque_amount DECIMAL(10,2) DEFAULT 0.00 AFTER cash_amount,
ADD COLUMN bank_transfer_amount DECIMAL(10,2) DEFAULT 0.00 AFTER cheque_amount;

-- Verify the new structure
DESCRIBE payment_receipts;

SELECT 'Payment method amount columns added successfully!' as status;