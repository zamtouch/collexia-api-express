function generateContractReference(merchantGid, baseDate = null, teller = null) {
  // Format: First 4 characters = Merchant GID in HEX
  //         Next 4 characters = Calculated base date (MMDD)
  //         Last 6 characters = Teller for the day
  // Example: 31240001000001 (14 characters total)
  
  // Convert merchant GID to 4-character hex (uppercase)
  const gidHex = parseInt(merchantGid).toString(16).toUpperCase().padStart(4, '0');
  
  // Use provided base date or today's date
  let datePart;
  if (baseDate === null) {
    const now = new Date();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    datePart = month + day; // MMDD
  } else {
    // Extract last 4 characters of date (MMDD from YYYYMMDD)
    datePart = baseDate.toString().slice(-4);
  }
  
  // Generate or use provided teller (6 digits)
  let tellerStr;
  if (teller === null) {
    // Generate a 6-digit number (combination of timestamp and random)
    const timestamp = Date.now() % 1000000;
    const random = Math.floor(Math.random() * 999);
    tellerStr = String(timestamp + random).padStart(6, '0').slice(-6);
  } else {
    tellerStr = String(teller).padStart(6, '0');
  }
  
  return gidHex + datePart + tellerStr;
}

module.exports = {
  generateContractReference
};

