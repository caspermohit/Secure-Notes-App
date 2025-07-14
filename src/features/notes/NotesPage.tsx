import React from 'react';

const NotesPage: React.FC = () => {
  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold mb-4">My Notes</h1>
      {/* TODO: Add notes list, search, filter, and create note UI */}
      <div className="bg-white dark:bg-gray-800 p-6 rounded shadow">
        <p className="text-gray-500 dark:text-gray-300">Notes list goes here.</p>
      </div>
    </div>
  );
};

export default NotesPage; 