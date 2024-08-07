import { useState, useEffect, useRef } from 'react';
import { colorClasses } from '../../data/colorClasses';
import DOMPurify from 'dompurify';
import { useColor } from '../../context/ColorContext';
import { useAuth } from '../auth/AuthContext';

interface Message {
  id: number;
  profilePicture?: string;
  sender?: string;
  content: string;
  sentAt?: {
    date: string;
    timezone_type: number;
    timezone: string;
  };
  reactions?: { [key: string]: number };

}

const ChatPanel: React.FC = () => {
  const { username } = useAuth();
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const [isLoading, setIsLoading] = useState(true);
  const container = useRef<HTMLDivElement>(null)

  const handleSendMessage = async () => {
    // filter out empty messages
    if (newMessage.trim() !== '') {
      const sanitizedMessage = DOMPurify.sanitize(newMessage);
      try {
        const response = await fetch(
          `http://127.0.0.1:8000/api/chatroom/?chatroomID=1`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
            body: JSON.stringify({ content: sanitizedMessage }),
          },
        );
        if (!response.ok) {
          throw new Error('HTTP error ' + response.status);
        }
        setNewMessage('');
        fetchMessages();
      } catch (error) {
        console.error(error);
      }
    }
  };

  const fetchMessages = async () => {
    setIsLoading(true);
    try {
      const response = await fetch(
        'http://127.0.0.1:8000/api/chatroom/?chatroomID=1',
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        },
      );

      if (!response.ok) {
        throw new Error('HTTP error ' + response.status);
      }

      const data = await response.json();
      // Map the fetched data to the Message structure
      const fetchedMessages: Message[] = data.map((message: Message) => ({
        id: message.id,
        sender: message.sender,
        content: message.content,
        sentAt: message.sentAt.date.split('.')[0],
        reactions: message.reactions,
        profilePicture: message.profilePicture,
      }));

      setMessages(fetchedMessages);
      setIsLoading(false);
      if (container.current) {
        container.current.scrollTop = container.current.scrollHeight;
      }
    } catch (error) {
      console.error(error);
    }
  };

  useEffect(() => {
    fetchMessages();
  }, []);

  // Subscribe to updates from the Mercure hub
  useEffect(() => {
    const url = new URL('http://localhost:3000/.well-known/mercure');
    url.searchParams.append('topic', '/chatroom/1'); // The topic to subscribe to
    const eventSource = new EventSource(url);

    eventSource.onmessage = () => {
      fetchMessages();
    };

    // Clean up the event source when the component is unmounted
    return () => {
      eventSource.close();
    };
  }, []); // Empty dependency array means this effect runs once on mount and clean up on unmount

  const handleAddReaction = (messageId: number, reactionCode: string) => {
    fetch(`http://127.0.0.1:8000/api/chatroom/1/message/${messageId}/reaction`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ reaction: reactionCode }),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        fetchMessages();
        return response.json();
      })
      .catch((error) => {
        console.error('There has been a problem with your fetch operation:', error);
      });

  };


  const { primaryColor } = useColor();
  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <div className="p-3 m-4 cursor-pointer rounded-md backdrop-blur-sm text-white bg-gray-900 shadow-inner shadow-white">
      <h2 className="text-xl font-semibold mb-2 text-center">Chat</h2>
      <div ref={container} className="overflow-auto h-80 mb-4 border-3 border-gray-900 p-4 bg-gray-700 rounded-md flex flex-col">
        {messages.map((message, index) => (
          <div
            key={index}
            className={`flex items-start space-x-4 my-4 ${message.sender === username ? 'justify-end' : 'justify-start'
              }`}
          >
            {message.sender !== username && (
              <img
                src={
                  message.profilePicture
                    ? `http://127.0.0.1:8000/profile-picture/${message.profilePicture}`
                    : '/assets/images/defaultUser.webp'
                }
                alt="Profile"
                className="h-10 w-10 object-cover rounded-full"
              />
            )}
            <div className={`p-3 rounded-lg ${message.sender === username ? 'bg-blue-500 text-white self-end' : 'bg-gray-800 text-white self-start'}`}>
              <span className="text-xs">
                {!isLoading && message.sentAt && !isNaN(new Date(message.sentAt).getTime())
                  ? new Intl.DateTimeFormat('en-UK', {
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                  }).format(new Date(message.sentAt))
                  : "Invalid date"}
              </span>
              <br />
              <strong>
                {message.sender === username ? 'You' : message.sender}
                <br />
              </strong>
              {message.content}
              {message.reactions && message.reactions.length > 0 && <br />}
              <span>
                {Array.isArray(message.reactions) &&
                  message.reactions
                    .filter((reaction) => reaction.reactionCode === '1')
                    .map((_, i) => (
                      <span key={i}>
                        {'👍' + message.reactions.filter((reaction) => reaction.reactionCode === '1').length}
                      </span>
                    ))}
                {Array.isArray(message.reactions) &&
                  message.reactions
                    .filter((reaction) => reaction.reactionCode === '2')
                    .map((_, i) => (
                      <span key={i}>
                        {'👎' + message.reactions.filter((reaction) => reaction.reactionCode === '2').length}
                      </span>
                    ))}
                {Array.isArray(message.reactions) &&
                  message.reactions
                    .filter((reaction) => reaction.reactionCode === '3')
                    .map((_, i) => (
                      <span key={i}>
                        {'😂' + message.reactions.filter((reaction) => reaction.reactionCode === '3').length}
                      </span>
                    ))}
                {Array.isArray(message.reactions) &&
                  message.reactions
                    .filter((reaction) => reaction.reactionCode === '4')
                    .map((_, i) => (
                      <span key={i}>
                        {'❤️' + message.reactions.filter((reaction) => reaction.reactionCode === '4').length}
                      </span>
                    ))}
              </span>
              <br />
              <span role="img" aria-label="thumbs up" onClick={() => handleAddReaction(message.id, '1')}>
                👍
              </span>
              <span role="img" aria-label="thumbs down" onClick={() => handleAddReaction(message.id, '2')}>
                👎
              </span>
              <span role="img" aria-label="laugh" onClick={() => handleAddReaction(message.id, '3')}>
                😂
              </span>
              <span role="img" aria-label="heart" onClick={() => handleAddReaction(message.id, '4')}>
                ❤️
              </span>
            </div>
            {message.sender === username && (
              <img
                src={
                  message.profilePicture
                    ? `http://127.0.0.1:8000/profile-picture/${message.profilePicture}`
                    : '/assets/images/defaultUser.webp'
                }
                alt="Profile"
                className="h-10 w-10 object-cover rounded-full ml-2"
              />
            )}
          </div>
        ))}
      </div>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleSendMessage();
        }}
        className="flex flex-col sm:flex-row"
      >
        <input
          type="text"
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
          className="border-2 border-gray-300 rounded-md p-2 flex-grow text-black sm:text-xs md:text-sm lg:text-base mb-2 sm:mb-0 flex-grow-0 sm:flex-grow"
        />
        <button
          type="submit"
          disabled={!newMessage}
          className={`ml-0 p-2 rounded-md ${colorClass} sm:ml-2 sm:mt-0 mt-2 flex-grow-0`}
        >
          Send
        </button>
      </form>
    </div>
  );

};

export default ChatPanel;
