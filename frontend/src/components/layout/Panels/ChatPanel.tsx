import { useState, useEffect } from 'react';
import { colorClasses } from '../../../data/colorClasses';

interface Message {
  sender?: string;
  content: string;
  sentAt?: string;
}

interface ChatPanelProps {
  color: string;
}

const ChatPanel: React.FC<ChatPanelProps> = ({ color }) => {
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');

  const handleSendMessage = async () => {
    if (newMessage.trim() !== '') {
      try {
        const response = await fetch('http://127.0.0.1:8000/chatroom/1', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
          body: JSON.stringify({ content: newMessage }),
        });

        if (!response.ok) {
          throw new Error('HTTP error ' + response.status);
        }

        setMessages([...messages, { content: newMessage }]);
        setNewMessage('');
      } catch (error) {
        console.error(error);
      }
    }
  };

  useEffect(() => {
    const fetchMessages = async () => {
      try {
        const response = await fetch('http://127.0.0.1:8000/chatroom/1', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });

        if (!response.ok) {
          throw new Error('HTTP error ' + response.status);
        }

        const data = await response.json();

        // Map the fetched data to the Message structure
        const fetchedMessages: Message[] = data.map((message: any) => ({
          sender: message.sender,
          content: message.content,
          sentAt: message.sentAt.date.split('.')[0],
        }));

        setMessages(fetchedMessages);
      } catch (error) {
        console.error(error);
      }
    };

    fetchMessages();
  }, []);

  const colorClass = color
    ? colorClasses[color as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <div className="p-5 m-6 cursor-pointer rounded-md backdrop-blur-sm text-white">
      <h2>Chat Panel</h2>
      <div className="overflow-auto h-64 mb-4 border-3 border-gray-900 bg-gray-700 rounded-md">
        {messages.map((message, index) => (
          <p key={index}>
            <strong>
              {message.sender}({message.sentAt}):
            </strong>{' '}
            {message.content}
          </p>
        ))}
      </div>
      <div className="flex">
        <input
          type="text"
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
          className="border-2 border-gray-300 rounded-md p-2 flex-grow text-black sm:text-xs md:text-sm lg:text-base"
        />
        <button
          onClick={handleSendMessage}
          disabled={!newMessage}
          className={`ml-2 p-2 rounded-md ${colorClass}`}
        >
          Send
        </button>
      </div>
    </div>
  );
};

export default ChatPanel;