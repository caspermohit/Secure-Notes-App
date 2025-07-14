const axios = require('axios');

const API_BASE_URL = 'http://localhost:8001/api';

async function testAuth() {
  console.log('🧪 Testing Authentication API...\n');

  try {
    // Test registration
    console.log('1. Testing registration...');
    const registerData = {
      name: 'Test User',
      email: 'test@example.com',
      password: 'password123',
      password_confirmation: 'password123'
    };

    const registerResponse = await axios.post(`${API_BASE_URL}/register`, registerData);
    console.log('✅ Registration successful:', registerResponse.data.message);
    console.log('User:', registerResponse.data.data.user.name);
    console.log('Token received:', registerResponse.data.data.token ? 'Yes' : 'No');
    console.log('');

    // Test login
    console.log('2. Testing login...');
    const loginData = {
      email: 'test@example.com',
      password: 'password123'
    };

    const loginResponse = await axios.post(`${API_BASE_URL}/login`, loginData);
    console.log('✅ Login successful:', loginResponse.data.message);
    console.log('User:', loginResponse.data.data.user.name);
    console.log('');

    // Test getting user with token
    console.log('3. Testing get user with token...');
    const token = loginResponse.data.data.token;
    const userResponse = await axios.get(`${API_BASE_URL}/user`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('✅ Get user successful:', userResponse.data.data.user.name);
    console.log('');

    // Test logout
    console.log('4. Testing logout...');
    const logoutResponse = await axios.post(`${API_BASE_URL}/logout`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    });
    console.log('✅ Logout successful:', logoutResponse.data.message);
    console.log('');

    console.log('🎉 All authentication tests passed!');

  } catch (error) {
    console.error('❌ Test failed:', error.response?.data || error.message);
  }
}

testAuth(); 