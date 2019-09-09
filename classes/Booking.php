<?php

include 'confirm-mail.php';

class Booking {
	private $connection;

	public function __construct($db) {
		$this->connection = $db;
	}

	public function deleteBooking($booking_ID) {
		$statement = $this->connection->prepare(
			'DELETE
			FROM bookings 
			WHERE booking_ID = ?'
		);

		$booking_ID=htmlspecialchars(strip_tags($booking_ID));
		$statement->bindParam(1, $booking_ID);
		
		if($statement->execute()) {
			return true;
		}

		return false;
	}

	public function updateBooking($booking_row) {
		
		$statement = $this->connection->prepare(
			"UPDATE bookings 
			SET customer_ID = :customer_ID, 
			guests = :guests, 
			sitting = :sitting
			WHERE booking_ID = :booking_ID"
		);

		if($statement->execute(
			[
				":booking_ID" => $booking_row->booking_ID,
				":customer_ID" => $booking_row->customer_ID,
				":guests" => $booking_row->guests,
				":sitting" => $booking_row->sitting				
			]
			)) {
				return true;
			}
	
		return false;
	}

	public function getBookedTables($date) {
		$statement = $this->connection->prepare(
			'SELECT * FROM bookings 
			WHERE DATE(sitting) = :date'
		);
			
		$statement->execute([
			':date' => $date
		]);
		$all_bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

		return $all_bookings;
	}

	public function getBookings() {
		// select all and prepare query statement
		$statement = $this->connection->prepare(
			'SELECT * FROM bookings
			LEFT JOIN customers
			ON bookings.customer_ID = customers.customer_ID'
		);

		// execute query
		$statement->execute();
		
		$all_bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

		return $all_bookings;
	}

	public function createBooking($booking_row) {
		$statement = $this->connection->prepare(
			'INSERT INTO bookings (customer_ID, guests, sitting) 
			VALUES (:customer_ID, :guests, :sitting)'
		); 

		// sanitize
		$booking_row->$customer_ID = htmlspecialchars(strip_tags($booking_row->$customer_ID));
		$booking_row->$guests = htmlspecialchars(strip_tags($booking_row->$guests));
		$booking_row->$sitting = htmlspecialchars(strip_tags($booking_row->$sitting));

		$statement->bindParam(':customer_ID', $booking_row->customer_ID);
		$statement->bindParam(':guests', $booking_row->guests);
		$statement->bindParam(':sitting', $booking_row->sitting); 

		if ($statement->execute()) {

			// $confirm = new Mailer();
			// $confirm->sendEmail($booking_row, $customer_row);

			$msg = "First line of text\nSecond line of text";

			// use wordwrap() if lines are longer than 70 characters
			$msg = wordwrap($msg,70);

			// send email
            mail("sygiel@hotmail.com","My subject",$msg);
            
            // if(mail("sygiel@hotmail.com","My subject",$msg)) {
            //     echo("Email sent");
            // }
            // else {
            //     echo('not sent');
            // }

			return true;
		}

		return false;
	}
}

class BookingRow {
	public $booking_ID;
	public $customer_ID;
	public $guests;
	public $sitting;
}