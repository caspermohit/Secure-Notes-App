import React, { useEffect } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import Toaster from 'react-hot-toast';
import { useAppDispatch, useAppSelector } from './hooks/redux';
import { getUser } from './features/auth/authSlice';
import LoginPage from './features/auth/LoginPage';
import RegisterPage from './features/auth/RegisterPage';
import NotesPage from './features/notes/NotesPage';
import NoteDetailPage from './features/notes/NoteDetailPage';
import Navbar from './components/Navbar';

// Protected Route Component
const ProtectedRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { user, token } = useAppSelector((state) => state.auth);
  
  if (!token || !user) {
    return <Navigate to="/login" />;
  }
  
  return <>{children}</>;
};

function App() {
  const dispatch = useAppDispatch();
  const { token } = useAppSelector((state) => state.auth);

  useEffect(() => {
    // Load user data if token exists
    if (token) {
      dispatch(getUser());
    }
  }, [dispatch, token]);

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
      <Navbar />
      <Toaster position="top-right" />
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />
        <Route 
          path="/notes" 
          element={
            <ProtectedRoute>
              <NotesPage />
            </ProtectedRoute>
          } 
        />
        <Route 
          path="/notes/:id" 
          element={
            <ProtectedRoute>
              <NoteDetailPage />
            </ProtectedRoute>
          } 
        />
        <Route path="/" element={<Navigate to="/notes" />} />
      </Routes>
    </div>
  );
}

export default App; 