-- Update payment_receipts table to have separate description fields for each payment method

-- First, rename the current description column to description_cash
ALTER TABLE payment_receipts CHANGE COLUMN description description_cash varchar(200);

-- Add new description columns for other payment methods
ALTER TABLE payment_receipts 
ADD COLUMN description_cheque varchar(200) DEFAULT '' AFTER description_cash,
ADD COLUMN description_bank_transfer varchar(200) DEFAULT '' AFTER description_cheque;