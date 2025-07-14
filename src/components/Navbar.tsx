import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAppDispatch, useAppSelector } from '../hooks/redux';
import { logout } from '../features/auth/authSlice';
import toast from 'react-hot-toast';

const Navbar: React.FC = () => {
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const { user } = useAppSelector((state) => state.auth);

  const handleLogout = async () => {
    try {
      await dispatch(logout()).unwrap();
      toast.success('Logged out successfully');
      navigate('/login');
    } catch (error) {
      toast.error('Logout failed');
    }
  };

  return (
    <nav className="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 shadow">
      <div className="flex items-center space-x-4">
        <Link to="/notes" className="font-bold text-lg text-blue-600 dark:text-blue-400">
          Secure Notes
        </Link>
      </div>
      <div className="flex items-center space-x-4">
        {user ? (
          <>
            <span className="text-sm text-gray-600 dark:text-gray-300">
              Welcome, {user.name}
            </span>
            <button
              onClick={handleLogout}
              className="px-3 py-1 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
            >
              Logout
            </button>
          </>
        ) : (
          <>
            <Link
              to="/login"
              className="px-3 py-1 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
            >
              Login
            </Link>
            <Link
              to="/register"
              className="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Register
            </Link>
          </>
        )}
        <button className="px-2 py-1 rounded bg-gray-200 dark:bg-gray-700">🌙</button>
      </div>
    </nav>
  );
};

export default Navbar; 