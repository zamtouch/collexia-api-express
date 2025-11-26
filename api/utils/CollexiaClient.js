const axios = require('axios');
const crypto = require('crypto');

class CollexiaClient {
  constructor() {
    // Use exact same config as PHP version
    this.config = {
      baseUrl: process.env.COLLEXIA_BASE_URL || 'https://collection-uat.collexia.co',
      basePath: process.env.COLLEXIA_BASE_PATH || '/api/coswitchuadsrest/v3',
      basicUser: process.env.COLLEXIA_BASIC_USER || 'bareinvuat',
      basicPass: process.env.COLLEXIA_BASIC_PASS || 'Ms@utbinT!11',
      clientId: process.env.COLLEXIA_CLIENT_ID || '6FA41D83-B8A5-11F0-B138-42010A960205',
      clientSecret: process.env.COLLEXIA_CLIENT_SECRET || '9FXhhuOtjiKinPFpbnSb',
      headerPrefix: process.env.COLLEXIA_HEADER_PREFIX || 'CX_SWITCH',
      merchantGid: parseInt(process.env.COLLEXIA_MERCHANT_GID || '12584'),
      remoteGid: parseInt(process.env.COLLEXIA_REMOTE_GID || '71')
    };
  }

  // Match PHP timestamp format: Y-m-d H:i:s.v (e.g., "2025-01-15 10:30:45.123456")
  nowTimestamp() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const milliseconds = String(now.getMilliseconds()).padStart(3, '0');
    const microseconds = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}.${milliseconds}${microseconds}`;
  }

  buildHeaders() {
    const dts = this.nowTimestamp();
    const msg = this.config.clientId + dts;
    const hmac = crypto.createHmac('sha512', this.config.clientSecret);
    hmac.update(msg);
    const hsh = hmac.digest('base64');
    
    const basicAuth = Buffer.from(`${this.config.basicUser}:${this.config.basicPass}`).toString('base64');
    
    return {
      'Authorization': `Basic ${basicAuth}`,
      [`${this.config.headerPrefix}_ClientId`]: this.config.clientId,
      [`${this.config.headerPrefix}_DTS`]: dts,
      [`${this.config.headerPrefix}_HSH`]: hsh,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    };
  }

  async loadMandate(payload, contractReference) {
    try {
      const url = `${this.config.baseUrl}${this.config.basePath}/mandates/load`;
      const response = await axios.post(url, payload, {
        headers: this.buildHeaders()
      });
      
      return {
        ok: true,
        data: response.data,
        status: response.status
      };
    } catch (error) {
      return {
        ok: false,
        error: error.response?.data || error.message,
        status: error.response?.status || 500
      };
    }
  }

  async checkMandateStatus(contractReference) {
    try {
      const url = `${this.config.baseUrl}${this.config.basePath}/mandates/finalfate`;
      const payload = {
        contractReference
      };
      
      const response = await axios.post(url, payload, {
        headers: this.buildHeaders()
      });
      
      return {
        ok: true,
        data: response.data,
        status: response.status
      };
    } catch (error) {
      return {
        ok: false,
        error: error.response?.data || error.message,
        status: error.response?.status || 500
      };
    }
  }

  async downloadPayments() {
    try {
      const url = `${this.config.baseUrl}${this.config.basePath}/payments/download`;
      const response = await axios.post(url, {}, {
        headers: this.buildHeaders()
      });
      
      return {
        ok: true,
        data: response.data,
        status: response.status
      };
    } catch (error) {
      return {
        ok: false,
        error: error.response?.data || error.message,
        status: error.response?.status || 500
      };
    }
  }
}

module.exports = CollexiaClient;

