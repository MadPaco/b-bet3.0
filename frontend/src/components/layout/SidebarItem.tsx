// src/components/SidebarItem.tsx
import React from 'react';

interface SidebarItemProps {
  icon: React.ReactNode;
  text: string;
  onClick: () => void;
  active: boolean;
  color?: string;
}

const SidebarItem: React.FC<SidebarItemProps> = ({ icon, text, onClick, active, color }) => {
  return (
    <li
      className={`flex items-center p-2 hover:bg-gray-700 rounded cursor-pointer ${active ? 'bg-gray-800' : ''}`}
      onClick={onClick}
      style={{ color }}
    >
      {icon}
      <span className="ml-4">{text}</span>
    </li>
  );
};

export default SidebarItem;
