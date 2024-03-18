import { useState, useEffect, useRef } from 'react';
import { colorClasses } from '../../../data/colorClasses';
import DOMPurify from 'dompurify';

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
  const chatBottom = useRef<null | HTMLDivElement>(null);

  const handleSendMessage = async () => {
    // filter out empty messages
    if (newMessage.trim() !== '') {
      //sanitize to avoid malicious shenaningans
      const sanitizedMessage = DOMPurify.sanitize(newMessage);
      try {
        const response = await fetch('http://127.0.0.1:8000/chatroom/1', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
          body: JSON.stringify({ content: sanitizedMessage }),
        });

        if (!response.ok) {
          throw new Error('HTTP error ' + response.status);
        }

        setNewMessage('');

        //scroll to bottom if new message is sent
        //the timeout is a workaround to ensure
        //that the fetch request has completed before scrolling

        if (chatBottom.current) {
          setTimeout(() => {
            if (chatBottom.current) {
              chatBottom.current.scrollIntoView({ behavior: 'smooth' });
            }
          }, 100);
        }
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

    // Subscribe to updates from the Mercure hub
    const url = new URL('http://localhost:3000/.well-known/mercure');
    url.searchParams.append('topic', '/chatroom/1'); // The topic to subscribe to
    const eventSource = new EventSource(url);

    eventSource.onmessage = (event) => {
      // When a new update is received, add the new message to the state
      const newMessage = JSON.parse(event.data);
      setMessages((messages) => [...messages, newMessage]);
    };

    // Clean up the EventSource when the component is unmounted
    return () => {
      eventSource.close();
    };
  }, []);

  const colorClass = color
    ? colorClasses[color as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  useEffect(() => {
    if (chatBottom.current) {
      chatBottom.current.scrollIntoView({ behavior: 'smooth' });
    }
  }, [messages]);

  return (
    <div className="p-5 m-6 cursor-pointer rounded-md backdrop-blur-sm text-white">
      <h2>Chat Panel</h2>
      <div className="overflow-auto h-64 mb-4 border-3 border-gray-900 bg-gray-700 rounded-md">
        {messages.map((message, index) => (
          <p key={index}>
            <strong>{message.sender}:</strong> {message.content}
          </p>
        ))}
        <div ref={chatBottom} />
      </div>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleSendMessage();
        }}
        className="flex"
      >
        <input
          type="text"
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
          className="border-2 border-gray-300 rounded-md p-2 flex-grow text-black sm:text-xs md:text-sm lg:text-base"
        />
        <button
          type="submit"
          disabled={!newMessage}
          className={`ml-2 p-2 rounded-md ${colorClass}`}
        >
          Send
        </button>
      </form>
    </div>
  );
};

export default ChatPanel;
