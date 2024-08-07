import React from 'react';
import { User } from '../../utility/types';

interface UserSelectProps {
  userList: User[];
  onUserSelect: (selectedUser: string) => void;
}

const UserSelect: React.FC<UserSelectProps> = ({ userList, onUserSelect }) => {
  return (
    <select
      className="text-black"
      onChange={(e) => {
        const selectedUser = userList.find(
          (user) => user.username === e.target.value,
        );
        if (selectedUser) {
          onUserSelect(selectedUser.username);
        }
      }}
    >
      <option value="">None</option>
      {userList.map((user: User) => {
        return (
          <option key={user.username} value={user.username}>
            {user.username}
          </option>
        );
      })}
    </select>
  );
};

export default UserSelect;
