//
//  LocalNotification.swift
//  DriverApp
//
//  Created by NEW MAC on 02/09/17.
//  Copyright © 2017 V3Cube All rights reserved.
//

import UIKit
import UserNotifications

class LocalNotification: NSObject, UNUserNotificationCenterDelegate {
    
    class func registerForLocalNotification(on application:UIApplication) {
        if (UIApplication.instancesRespond(to: #selector(UIApplication.registerUserNotificationSettings(_:)))) {
            let notificationCategory:UIMutableUserNotificationCategory = UIMutableUserNotificationCategory()
            notificationCategory.identifier = "NOTIFICATION_CATEGORY"
            
            //registerting for the notification.
            application.registerUserNotificationSettings(UIUserNotificationSettings(types:[.sound, .alert, .badge], categories: nil))
        }
    }
    
    class func dispatchlocalNotification(with title: String, body: String, userInfo: [AnyHashable: Any]? = nil, at date:Date) {
        
        
        Utils.resetAppNotifications()
        
        
        let notification = UILocalNotification()
        notification.fireDate = date
        notification.alertTitle = title
        notification.alertBody = body
        
        if let info = userInfo {
            notification.userInfo = info
        }
        notification.applicationIconBadgeNumber = 1
        notification.soundName = UILocalNotificationDefaultSoundName
        UIApplication.shared.scheduleLocalNotification(notification)
        
        //        Utils.printLog(msgData: "WILL DISPATCH LOCAL NOTIFICATION AT \(date)")
        
    }
    
    class func dispatchlocalNotification(with title: String, body: String, userInfo: [AnyHashable: Any]? = nil, at date:Date, onlyInBackground:Bool) {
        
        if(onlyInBackground == true && Utils.isMyAppInBackground() == false){
            return
        }
        Utils.resetAppNotifications()
        
        
        let notification = UILocalNotification()
        notification.fireDate = date
        notification.alertTitle = title
        notification.alertBody = body
        
        if let info = userInfo {
            notification.userInfo = info
        }
        notification.applicationIconBadgeNumber = 1
        notification.soundName = UILocalNotificationDefaultSoundName
        UIApplication.shared.scheduleLocalNotification(notification)
        
        //        Utils.printLog(msgData: "WILL DISPATCH LOCAL NOTIFICATION AT \(date)")
        
    }
}
