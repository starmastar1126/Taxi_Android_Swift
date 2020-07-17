//
//  NSMDataExt.swift
//  UberCloneApp
//
//  Created by Chirag on 19/12/15.
//  Copyright © 2015 ESW. All rights reserved.
//

import UIKit

extension NSMutableData {
    
    func appendString(string: String) {
        let data = string.data(using: String.Encoding.utf8, allowLossyConversion: true)
        append(data!)
    }
}
