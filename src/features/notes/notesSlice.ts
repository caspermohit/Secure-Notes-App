import { createSlice } from '@reduxjs/toolkit';

const initialState = {
  notes: [],
  loading: false,
  error: null,
};

const notesSlice = createSlice({
  name: 'notes',
  initialState,
  reducers: {
    // TODO: Add reducers for CRUD operations
  },
});

export default notesSlice.reducer; 