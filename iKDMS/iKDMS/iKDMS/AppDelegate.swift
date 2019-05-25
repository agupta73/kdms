//
//  AppDelegate.swift
//  iKDMS
//
//  Created by Gupta, Anil on 1/23/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import CoreData
import Alamofire

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate {

    var window: UIWindow?


    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplicationLaunchOptionsKey: Any]?) -> Bool {
        //Alamofire.request(<#T##url: URLConvertible##URLConvertible#>)
        // create UIWindow with the same size as main screen
        //window = UIWindow(frame: UIScreen.mainScreen().bounds)
        window = UIWindow(frame: UIScreen.main.bounds)
        
        // create story board. Default story board will be named as Main.storyboard in your project.
        let storyboard = UIStoryboard(name: "Main", bundle: nil)
        
        // create view controllers from storyboard
        // Make sure you set Storyboard ID for both the viewcontrollers in
        // Interface Builder -> Identitiy Inspector -> Storyboard ID
        //let clockViewController = storyboard.instantiateViewControllerWithIdentifier("ClockViewController")
        //let stopWatchViewController = storyboard.instantiateViewControllerWithIdentifier("StopWatchViewController")
        let devoteeListViewController = storyboard.instantiateViewController(withIdentifier: "DevoteeNavigationBar") as! UINavigationController
        let devoteeListViewController2 = storyboard.instantiateViewController(withIdentifier: "DevoteeNavigationBar") as! UINavigationController
        let devoteeListViewController3 = storyboard.instantiateViewController(withIdentifier: "DevoteeSearchViewController") as! UIViewController
        let devoteeListViewController4 = storyboard.instantiateViewController(withIdentifier: "ReportViewController") as! UITableViewController
        let devoteeViewController = storyboard.instantiateViewController(withIdentifier:"DevoteeViewController")
        
        devoteeListViewController.tabBarItem.title = "Print List"
        devoteeListViewController2.tabBarItem.title = "Add Photo"
        devoteeListViewController3.tabBarItem.title = "Search"
        devoteeListViewController4.tabBarItem.title = "View Report"
        //devoteeListViewController.tabBarItem.image = UIImage(named: "List")
        
        //devoteeViewController.tabBarItem.title = "Register"
        //devoteeViewController.tabBarItem.image = UIImage(named: "Detail")
        //devoteeViewController.tabBarItem.badgeColor = UIColor.clear
        
        // Set up the Tab Bar Controller to have two tabs
        let tabBarController = UITabBarController()
        tabBarController.viewControllers = //[devoteeListViewController,devoteeListViewController2,devoteeListViewController3, devoteeViewController]
        [devoteeListViewController,devoteeListViewController2,devoteeListViewController3,devoteeListViewController4]
        
        // Make the Tab Bar Controller the root view controller
        window?.rootViewController = tabBarController
        window?.makeKeyAndVisible()
 
        return true
    }

    func applicationWillResignActive(_ application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and invalidate graphics rendering callbacks. Games should use this method to pause the game.
    }

    func applicationDidEnterBackground(_ application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
    }

    func applicationWillEnterForeground(_ application: UIApplication) {
        // Called as part of the transition from the background to the active state; here you can undo many of the changes made on entering the background.
    }

    func applicationDidBecomeActive(_ application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    }

    func applicationWillTerminate(_ application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
        // Saves changes in the application's managed object context before the application terminates.
        self.saveContext()
    }

    // MARK: - Core Data stack

    lazy var persistentContainer: NSPersistentContainer = {
        /*
         The persistent container for the application. This implementation
         creates and returns a container, having loaded the store for the
         application to it. This property is optional since there are legitimate
         error conditions that could cause the creation of the store to fail.
        */
        let container = NSPersistentContainer(name: "iKDMS")
        container.loadPersistentStores(completionHandler: { (storeDescription, error) in
            if let error = error as NSError? {
                // Replace this implementation with code to handle the error appropriately.
                // fatalError() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
                 
                /*
                 Typical reasons for an error here include:
                 * The parent directory does not exist, cannot be created, or disallows writing.
                 * The persistent store is not accessible, due to permissions or data protection when the device is locked.
                 * The device is out of space.
                 * The store could not be migrated to the current model version.
                 Check the error message to determine what the actual problem was.
                 */
                fatalError("Unresolved error \(error), \(error.userInfo)")
            }
        })
        return container
    }()

    // MARK: - Core Data Saving support

    func saveContext () {
        let context = persistentContainer.viewContext
        if context.hasChanges {
            do {
                try context.save()
            } catch {
                // Replace this implementation with code to handle the error appropriately.
                // fatalError() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
                let nserror = error as NSError
                fatalError("Unresolved error \(nserror), \(nserror.userInfo)")
            }
        }
    }

}

