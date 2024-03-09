import Header from "./Header";
import Footer from "./Footer";

interface LayoutProps{
    content: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ content }) =>{
    return (
        <div>
            <Header/>
            {content}
            <Footer />
        </div>
    );

}

export default Layout;