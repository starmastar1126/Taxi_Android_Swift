//
//  NSdataExt.swift
//  PassengerApp
//
//  Created by Chirag on 19/08/16.
//  Copyright © 2016 BBCS. All rights reserved.
//

import Foundation
extension Data {
    var attributedString: NSAttributedString? {
        do {
            return try NSAttributedString(data: self, options:[NSDocumentTypeDocumentAttribute:NSHTMLTextDocumentType, NSCharacterEncodingDocumentAttribute: String.Encoding.utf8], documentAttributes: nil)
        } catch let error as NSError {
            print(error.localizedDescription)
        }
        return nil
    }
}
