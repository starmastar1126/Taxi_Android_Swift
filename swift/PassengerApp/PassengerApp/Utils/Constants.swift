//
//  Constants.swift
//  PassengerApp
//
//  Created by NEW MAC on 01/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
import Firebase

struct Constants
{
    struct refs
    {
        static let databaseRoot = Database.database().reference()
        static let databaseChats = databaseRoot.child("\((GeneralFunctions.getValue(key: Utils.APP_GCM_SENDER_ID_KEY) as! String))-chat")
    }
}
