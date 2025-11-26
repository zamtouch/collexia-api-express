// Validation utilities

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validateBankId(bankId) {
  const validBankIds = [64, 65, 66, 67, 68, 69, 70, 71, 72];
  return validBankIds.includes(parseInt(bankId));
}

function validateAccountType(accountType) {
  const validTypes = [1, 2, 3]; // 1=Current, 2=Savings, 3=Transmission
  return validTypes.includes(parseInt(accountType));
}

function sanitize(input) {
  if (typeof input !== 'string') return input;
  return input.trim().replace(/[<>]/g, '');
}

module.exports = {
  validateEmail,
  validateBankId,
  validateAccountType,
  sanitize
};

