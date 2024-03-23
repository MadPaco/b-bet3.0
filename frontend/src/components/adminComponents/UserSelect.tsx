import React from 'react';

interface User {
  username: string;
  favTeam: string;
}

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
