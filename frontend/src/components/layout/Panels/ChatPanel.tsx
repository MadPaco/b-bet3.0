import { useState, useEffect } from 'react';
import { colorClasses } from '../../../data/colorClasses';
import { useAuth } from '../../auth/AuthContext';
import { fetchTeamInfo } from '../../../utility/api';

interface Message {
  sender: string;
  content: string;
}

const ChatPanel: React.FC = () => {
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const [primaryColor, setPrimaryColor] = useState<string | null>(null);
  const { favTeam } = useAuth();

  const handleSendMessage = () => {
    // Add the new message to the messages array
    setMessages([...messages, { sender: 'You', content: newMessage }]);
    // Clear the input field
    setNewMessage('');
  };

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setPrimaryColor(data.primaryColor))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <div className="p-5 m-6 cursor-pointer rounded-md backdrop-blur-sm text-white">
      <h2>Chat Panel</h2>
      <div className="overflow-auto h-64 mb-4 border-3 border-gray-900 bg-gray-700 rounded-md">
        {messages.map((message, index) => (
          <p key={index}>
            <strong>{message.sender}:</strong> {message.content}
          </p>
        ))}
      </div>
      <div className="flex">
        <input
          type="text"
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
          className="border-2 border-gray-300 rounded-md p-2 flex-grow"
          placeholder="Type your message here..."
        />
        <button
          onClick={handleSendMessage}
          className={`ml-2 p-2 text-white rounded-md h-full ${colorClass}`}
        >
          Send
        </button>
      </div>
    </div>
  );
};
export default ChatPanel;
