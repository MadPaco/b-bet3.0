import Navigation from "./Navigation";


const Header: React.FC = () =>{
    return(
        <header className="bg-black flex p-2 justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-500">B-Bet</h1>
            <Navigation />
        </header>
    )
}

export default Header;