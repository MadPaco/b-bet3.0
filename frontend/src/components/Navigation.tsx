import { useState } from 'react';
import Dropdown from './Dropdown';

const Navigation: React.FC = () => {
  const [openDropdown, setOpenDropdown] = useState<string | null>(null);

  const handleDropdown = (title: string) => {
    setOpenDropdown(openDropdown === title ? null : title);
  };

  return (
    <nav className="flex space-x-3">
      <Dropdown
        title="Bets etc"
        items={[
          'Schedule',
          'Place Predictions',
          'Community Predictions',
          'Standings',
          'Advanced Stats',
          '1v1 Bets',
        ]}
        onToggle={() => handleDropdown('Bets etc')}
        isOpen={openDropdown === 'Bets etc'}
      />
      <Dropdown
        title="Social"
        items={['My Profile', 'Users', 'Chat']}
        onToggle={() => handleDropdown('Social')}
        isOpen={openDropdown === 'Social'}
      />
      <Dropdown
        title="Admin Panel"
        items={['Manage Users', 'Edit/Add Games', 'Enter results']}
        onToggle={() => handleDropdown('Admin Panel')}
        isOpen={openDropdown === 'Admin Panel'}
      />
    </nav>
  );
};

export default Navigation;
