from src.anekay import price_updater_bp
from src.anekay import desc_updater
from src.anekay import inactivate_products
from src.anekay import refresh_prices

def update_data():
    user_option = get_user_inputs()
    if(user_option.strip() == '1'):
        desc_updater.update_data()
    elif(user_option.strip() == '2'):
        price_updater_bp.update_data()
    elif(user_option.lower().strip() == '3'):
        inactivate_products.update_data()
    elif(user_option.lower().strip() == '4'):
        refresh_prices.update_data()
    elif(user_option.lower().strip() == 'x'):
        print("Thanks for using PIM system!")
    else:
        print("No option entered. Please run the program again!")
 
def get_user_inputs():
    while True:
        try:
            user_option = input("Please select the product information you want to update.\n============================================================\n" \
            "1. Update description of the new products (generated using chatGPT) as well as other info.\n" \
            "2. Update prices to reflect Google's recommendations.\n" \
            "3. Inactivate FashionGo products that are no longer available.\n" \
            "4. Refresh priceses to ensure uniformity.\n"
            "x. Exit without doing anything.\n" \
            "Enter Option: ")
            
            if (user_option == "" ):
                print("Please sepfcify the option.")
            else:
                break # Exit the loop if inputs are valid
        except ValueError:
            print("Invalid Input")
            return "x"
    return user_option